<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Antrian;
use App\Models\Pegawai;
use App\Models\RekamMedis;
use App\Models\Feedback;

class AntrianController extends Controller
{
    public function index()
    {
        $antrians = Antrian::with('pasien')
            ->where('tanggal', now()->toDateString())
            ->orderByRaw("
                CASE
                  WHEN LOWER(status) = 'dipanggil' THEN 0
                  WHEN LOWER(status) = 'menunggu'  THEN 1
                  WHEN LOWER(status) IN ('selesai','batal') THEN 3
                  ELSE 2
                END ASC
            ")
            ->orderBy('no_antrian')
            ->get();

        $dokters = Pegawai::whereHas('user', function($q) {
            $q->where('jabatan_id', '1');
        })->get();

        return view('admin.pemesanan', [
            'antrians' => $antrians,
            'dokters' => $dokters,
            'jumlahAntrian' => $antrians->count(),
            'terpanggil' => $antrians->where('status', 'Dipanggil')->count(),
            'selesai' => $antrians->where('status', 'Selesai')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pasien_id' => 'required|exists:pasien,id',
            'jenis_pemesan' => 'required|in:Offline',
            'status' => 'required|in:Menunggu',
            'catatan' => 'nullable|string|max:255',
        ]);

        $lastAntrian = Antrian::where('tanggal', now()->toDateString())
            ->orderBy('no_antrian', 'desc')
            ->first();

        $nextNo = $lastAntrian ? $lastAntrian->no_antrian + 1 : 1;

        Antrian::create([
            'no_antrian' => $nextNo,
            'pasien_id' => $request->pasien_id,
            'tanggal' => now()->toDateString(),
            'jenis' => $request->jenis_pemesan,
            'keluhan' => $request->catatan,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pemesanan')->with('success', 'Antrian berhasil ditambahkan.');
    }

    public function panggilPeriksa(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);

        $request->validate([
            'dokter_id' => 'required|exists:pegawai,id',
            'tekanan_darah' => 'required|string|max:20',
            'suhu' => 'required|numeric|min:30|max:45',
            'berat_badan' => 'required|numeric|min:1|max:200',
            'tinggi_badan' => 'required|numeric|min:30|max:250',
            'nadi' => 'required|integer|min:40|max:200',
            'respirasi' => 'required|integer|min:10|max:60',
        ]);


        DB::transaction(function () use ($antrian, $request) {
            $antrian->update(['status' => 'Dipanggil']);

            RekamMedis::create([
                'antrian_id' => $antrian->id,
                'pasien_id' => $antrian->pasien_id,
                'dokter_id' => $request->dokter_id,
                'tanggal_periksa' => now(),
                'tekanan_darah' => $request->tekanan_darah,
                'suhu' => $request->suhu,
                'berat_badan' => $request->berat_badan,
                'tinggi_badan' => $request->tinggi_badan,
                'nadi' => $request->nadi,
                'respirasi' => $request->respirasi,
            ]);
        });

        return redirect()->route('admin.pemesanan')->with('success', 'Pasien berhasil dipanggil dan TTV telah disimpan.');
    }

    /**
     * Simpan antrian online dari portal pasien.
     * Dipanggil via AJAX (fetch) dari dashboard_pasien.blade.php.
     */
    public function storePasien(Request $request)
    {
        // Pastikan pasien sudah login dan memiliki data pasien
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Data pasien tidak ditemukan. Hubungi admin.',
            ], 422);
        }

        $request->validate([
            'jenis'   => 'required|string|max:100',
            'keluhan' => 'nullable|string|max:1000',
        ]);

        // Cegah duplikat antrian aktif pada hari yang sama
        $sudahAda = Antrian::where('pasien_id', $pasien->id)
            ->where('tanggal', now()->toDateString())
            ->whereIn('status', ['Menunggu', 'Dipanggil'])
            ->whereNull('deleted_at')
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki antrian aktif hari ini.',
            ], 422);
        }

        // Hitung nomor antrian berikutnya untuk hari ini
        $lastAntrian = Antrian::where('tanggal', now()->toDateString())
            ->whereNull('deleted_at')
            ->orderBy('no_antrian', 'desc')
            ->first();

        $nextNo = $lastAntrian ? $lastAntrian->no_antrian + 1 : 1;

        $antrian = Antrian::create([
            'no_antrian' => $nextNo,
            'pasien_id'  => $pasien->id,
            'jenis'      => 'Online',
            'keluhan'    => $request->keluhan ?: null,
            'status'     => 'Menunggu',
            'tanggal'    => now()->toDateString(),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Antrian berhasil diambil!',
            'id'         => $antrian->id,
            'no_antrian' => str_pad($antrian->no_antrian, 3, '0', STR_PAD_LEFT),
            'layanan'    => $request->jenis,
            'tanggal'    => $antrian->tanggal->format('d M Y'),
        ]);
    }

    /**
     * Batalkan dan hapus permanen antrian online milik pasien.
     * Hanya bisa dibatalkan jika status masih 'Menunggu'.
     */
    public function cancelPasien(Request $request, $id)
    {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Data pasien tidak ditemukan.',
            ], 422);
        }

        // Gunakan withTrashed agar bisa menemukan record soft-deleted jika ada
        $antrian = Antrian::withTrashed()
            ->where('id', $id)
            ->where('pasien_id', $pasien->id)
            ->first();

        if (!$antrian) {
            return response()->json([
                'success' => false,
                'message' => 'Antrian tidak ditemukan.',
            ], 404);
        }

        // Gunakan getRawOriginal agar tidak terpengaruh Eloquent accessor
        $statusRaw = $antrian->getRawOriginal('status');
        if (!in_array($statusRaw, ['Menunggu'])) {
            return response()->json([
                'success' => false,
                'message' => 'Antrian sudah diproses dan tidak bisa dibatalkan.',
            ], 422);
        }

        // Hard delete: hapus permanen dari tabel
        $antrian->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Antrian berhasil dibatalkan.',
        ]);
    }

    /**
     * Store feedback dari pasien.
     */
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'kritik' => 'nullable|string',
            'saran' => 'nullable|string',
            'antrian_id' => 'required|exists:antrian,id',
        ]);

        $user = auth()->user();
        if (!$user || !$user->pasien) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $antrian = Antrian::find($request->antrian_id);
        
        // Cek jika antrian milik pasien ini
        if ($antrian->pasien_id !== $user->pasien->id) {
            return response()->json(['success' => false, 'message' => 'Invalid queue'], 403);
        }

        $rekamMedisId = null;
        if ($antrian->rekamMedis) {
            $rekamMedisId = $antrian->rekamMedis->id;
        }

        Feedback::create([
            'pasien_id' => $user->pasien->id,
            'rekam_medis_id' => $rekamMedisId,
            'kritik' => $request->kritik,
            'saran' => $request->saran,
            'penilaian' => $request->rating,
        ]);

        return response()->json(['success' => true, 'message' => 'Feedback berhasil disimpan']);
    }

    public function realtimeData()
    {
        $antrians = Antrian::with('pasien')
            ->where('tanggal', now()->toDateString())
            ->orderByRaw("
                CASE
                  WHEN LOWER(status) = 'dipanggil' THEN 0
                  WHEN LOWER(status) = 'menunggu'  THEN 1
                  WHEN LOWER(status) IN ('selesai','batal') THEN 3
                  ELSE 2
                END ASC
            ")
            ->orderBy('no_antrian')
            ->get();

        $hasUpdate = auth()->user() && auth()->user()->hasMenuAccess('Antrian Pemesanan', 'update');

        $html = '';
        foreach ($antrians as $a) {
            $jenis = strtolower($a->jenis_pemesan ?? 'offline');
            $jenisHtml = '';
            if ($jenis === 'online') {
                $jenisHtml = '<span class="jenis-online text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-wifi text-xs mr-1"></i>Online</span>';
            } elseif ($jenis === 'walk-in' || $jenis === 'walkin') {
                $jenisHtml = '<span class="jenis-walkin text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-walking text-xs mr-1"></i>Walk-in</span>';
            } else {
                $jenisHtml = '<span class="jenis-offline text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-phone text-xs mr-1"></i>Offline</span>';
            }

            $st = strtolower($a->status ?? 'menunggu');
            $statusHtml = '';
            if ($st === 'menunggu') {
                $statusHtml = '<span class="status-badge s-menunggu">Menunggu</span>';
            } elseif ($st === 'dipanggil') {
                $statusHtml = '<span class="status-badge s-dipanggil">Dipanggil</span>';
            } elseif ($st === 'dilayani') {
                $statusHtml = '<span class="status-badge s-dilayani">Dilayani</span>';
            } elseif ($st === 'selesai') {
                $statusHtml = '<span class="status-badge s-selesai">Selesai</span>';
            } elseif ($st === 'batal') {
                $statusHtml = '<span class="status-badge s-batal">Batal</span>';
            }

            $buttonsHtml = '';
            if ($hasUpdate) {
                $buttonsHtml = '<td class="px-5 py-3.5"><div class="flex items-center gap-2">';
                if ($st === 'menunggu') {
                    $buttonsHtml .= '<button type="button" class="btn-panggil" onclick="openPanggil(' . $a->id . ', \'' . addslashes($a->pasien->nama ?? '') . '\')" title="Panggil Pasien"><i class="fas fa-bullhorn text-xs"></i> Panggil</button>';
                }
                if (!in_array($st, ['selesai', 'batal'])) {
                    $buttonsHtml .= '<button class="btn-batal" onclick="openBatal(' . $a->id . ', \'' . addslashes($a->pasien->nama ?? '') . '\')" title="Batalkan"><i class="fas fa-times text-xs"></i></button>';
                }
                $buttonsHtml .= '</div></td>';
            }

            $genderHtml = ($a->pasien->jenis_kelamin ?? '') === 'L' 
                ? '<span class="text-xs font-bold px-3 py-1 rounded-full" style="background:#eff6ff;color:#2563eb">♂ Laki-laki</span>'
                : '<span class="text-xs font-bold px-3 py-1 rounded-full" style="background:#f5f3ff;color:#7c3aed">♀ Perempuan</span>';

            $waktuPesan = $a->created_at ? \Carbon\Carbon::parse($a->created_at)->isoFormat('D MMM · HH:mm') : '—';
            $noRM = $a->pasien->no_rm ?? '—';
            $namaPasien = $a->pasien->nama ?? '—';

            $html .= '
              <tr class="tbl-row" data-status="' . strtolower($a->status) . '">
                <td class="px-5 py-3.5"><div class="no-antrian">' . $a->nomor_antrian . '</div></td>
                <td class="px-5 py-3.5"><span class="text-blue-700 font-bold font-mono text-xs tracking-wide">' . $noRM . '</span></td>
                <td class="px-5 py-3.5"><div class="font-semibold text-gray-800 text-sm">' . $namaPasien . '</div></td>
                <td class="px-5 py-3.5">' . $genderHtml . '</td>
                <td class="px-5 py-3.5 text-gray-500 text-xs">' . $waktuPesan . '</td>
                <td class="px-5 py-3.5">' . $jenisHtml . '</td>
                <td class="px-5 py-3.5">' . $statusHtml . '</td>
                ' . $buttonsHtml . '
              </tr>
            ';
        }

        if ($antrians->isEmpty()) {
            $colspan = $hasUpdate ? 8 : 7;
            $html = '
              <tr id="emptyRow">
                <td colspan="' . $colspan . '" class="text-center py-16 text-gray-400">
                  <i class="fas fa-inbox text-4xl mb-4 block opacity-25"></i>
                  <p class="font-semibold text-sm">Belum ada antrian hari ini</p>
                </td>
              </tr>
            ';
        }

        return response()->json([
            'html' => $html,
            'jumlahAntrian' => $antrians->count(),
            'terpanggil' => $antrians->where('status', 'Dipanggil')->count(),
        ]);
    }

    // Placeholder untuk update status
    public function updateStatus(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Menunggu,Terpanggil,Dipanggil,Dilayani,Selesai,Batal',
        ]);

        $antrian->update(['status' => $request->status]);

        $message = 'Status antrian berhasil diupdate.';
        return redirect()->route('admin.pemesanan')->with('success', $message);
    }
}