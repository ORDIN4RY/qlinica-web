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
        $antrians = Antrian::with(['pasien', 'rekamMedis'])
            ->where('tanggal', now()->toDateString())
            ->orderByRaw("
                CASE
                  WHEN LOWER(status) = 'dipanggil' AND NOT EXISTS (SELECT 1 FROM rekam_medis WHERE rekam_medis.antrian_id = antrian.id AND rekam_medis.deleted_at IS NULL) THEN 0
                  WHEN LOWER(status) = 'menunggu' THEN 1
                  WHEN LOWER(status) = 'dipanggil' AND EXISTS (SELECT 1 FROM rekam_medis WHERE rekam_medis.antrian_id = antrian.id AND rekam_medis.deleted_at IS NULL) THEN 2
                  WHEN LOWER(status) = 'dilayani' THEN 3
                  WHEN LOWER(status) IN ('selesai','batal') THEN 4
                  ELSE 5
                END ASC
            ")
            ->orderBy('no_antrian')
            ->get();

        $dokters = Pegawai::whereHas('user', function($q) {
            $q->where('jabatan_id', '2');
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
            'dokter_id' => 'nullable|exists:pegawai,id',
            'jenis_pelayanan' => 'required|in:Umum,BPJS',
            'pelayanan_kesehatan' => 'required|in:Poli Umum,Poli Gigi,Poli KIA,UGD,Laboratorium,Baby Spa',
            'tekanan_darah' => 'required|string|max:10',
            'suhu' => 'required|numeric|min:30|max:45',
            'berat_badan' => 'required|numeric|min:1|max:300',
            'tinggi_badan' => 'required|numeric|min:30|max:300',
            'nadi' => 'required|integer|min:30|max:200',
            'respirasi' => 'required|integer|min:10|max:60',
            'no_bpjs' => 'required_if:jenis_pelayanan,BPJS|nullable|string',
        ]);

        if ($request->jenis_pelayanan === 'BPJS') {
            $noBpjs = $request->input('no_bpjs');
            $isNik = (strlen($noBpjs) === 16);
            
            $statusKeterangan = 'AKTIF';
            
            // Integrasi PCare BPJS
            $bpjsModeProduction = strtolower(env('BPJS_MODE', 'sandbox')) === 'production';
            try {
                if ($bpjsModeProduction && class_exists('\Bridging\Bpjs\PCare\Peserta') && env('BPJS_PCARE_CONSID')) {
                    $config = [
                        'cons_id'      => env('BPJS_PCARE_CONSID'),
                        'secret_key'   => env('BPJS_PCARE_SECRET_KEY'),
                        'username'     => env('BPJS_PCARE_USERNAME'),
                        'password'     => env('BPJS_PCARE_PASSWORD'),
                        'app_code'     => env('BPJS_PCARE_APP_CODE'),
                        'base_url'     => env('BPJS_PCARE_BASE_URL'),
                        'service_name' => env('BPJS_PCARE_SERVICE_NAME'),
                        'user_key'     => env('BPJS_PCARE_USER_KEY'),
                        'antrean_user_key' => env('BPJS_PCARE_ANTREAN_USER_KEY'),
                    ];
                    $bpjs = new \Bridging\Bpjs\PCare\Peserta($config);
                    
                    $jenisKartu = $isNik ? 'nik' : 'noka';
                    $res = $bpjs->jenisKartu($jenisKartu)->keyword($noBpjs)->show();
                    
                    if (isset($res['metaData']['code']) && $res['metaData']['code'] == 200) {
                        $peserta = $res['response'] ?? null;
                        if ($peserta) {
                            $statusKeterangan = $peserta['statusPeserta']['keterangan'] ?? 'AKTIF';
                        }
                    } else {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'no_bpjs' => 'BPJS Kesehatan: ' . ($res['metaData']['message'] ?? 'Kartu/NIK tidak terdaftar.'),
                        ]);
                    }
                } else {
                    // Fallback Uji Coba: NIK terdiri dari 16 digit, No. Kartu BPJS terdiri dari 13 digit
                    if (strlen($noBpjs) !== 13 && strlen($noBpjs) !== 16) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'no_bpjs' => 'Format tidak dikenali. Masukkan 13 digit Nomor Kartu atau 16 digit NIK.',
                        ]);
                    }
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                throw $e;
            } catch (\Exception $e) {
                if (strlen($noBpjs) !== 13 && strlen($noBpjs) !== 16) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'no_bpjs' => 'Format tidak valid & koneksi gagal: ' . $e->getMessage(),
                    ]);
                }
            }

            if (strtoupper($statusKeterangan) !== 'AKTIF') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'no_bpjs' => "Kartu BPJS ditemukan namun status kepesertaan TIDAK AKTIF ({$statusKeterangan}). Harap ubah Jenis Pelayanan menjadi Umum.",
                ]);
            }
        }


        DB::transaction(function () use ($antrian, $request) {
            $antrian->update(['status' => 'Dipanggil']);

            if ($request->jenis_pelayanan === 'BPJS' && $request->filled('no_bpjs')) {
                $antrian->pasien->update(['no_bpjs' => $request->input('no_bpjs')]);
            }

            RekamMedis::create([
                'antrian_id' => $antrian->id,
                'pasien_id' => $antrian->pasien_id,
                'dokter_id' => $request->dokter_id ?: null,
                'jenis_pelayanan' => $request->jenis_pelayanan,
                'pelayanan_kesehatan' => $request->pelayanan_kesehatan,
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

        // Cegah jika pasien masih terdaftar dalam perawatan aktif (Rawat Inap atau Billing Belum Lunas)
        $rawatInapAktif = \App\Models\RawatInap::where('pasien_id', $pasien->id)
            ->where('status', 'Aktif')
            ->exists();

        $billingBelumLunas = \App\Models\Billing::where('pasien_id', $pasien->id)
            ->where('status', 'Belum Bayar')
            ->exists();

        if ($rawatInapAktif || $billingBelumLunas) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih terdaftar dalam pelayanan/perawatan aktif di klinik. Selesaikan administrasi terlebih dahulu.',
            ], 422);
        }

        // Cegah duplikat antrian aktif pada hari yang sama
        $sudahAda = Antrian::where('pasien_id', $pasien->id)
            ->where('tanggal', now()->toDateString())
            ->whereIn('status', ['Menunggu', 'Dipanggil', 'Dilayani'])
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
            'ulasan' => 'nullable|string',
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

        // Cegah duplikat ulasan
        if ($rekamMedisId) {
            $sudahDiulas = Feedback::where('rekam_medis_id', $rekamMedisId)->exists();
            if ($sudahDiulas) {
                return response()->json(['success' => false, 'message' => 'Anda sudah memberikan ulasan untuk kunjungan ini.'], 422);
            }
        }

        Feedback::create([
            'pasien_id' => $user->pasien->id,
            'rekam_medis_id' => $rekamMedisId,
            'ulasan' => $request->ulasan,
            'penilaian' => $request->rating,
        ]);

        return response()->json(['success' => true, 'message' => 'Feedback berhasil disimpan']);
    }

    /**
     * Halaman display antrian publik (tanpa login).
     */
    public function displayPage()
    {
        return view('antrian_display');
    }

    /**
     * API data antrian untuk layar display publik (polling).
     * Mengembalikan nomor yang sedang dipanggil, statistik, dan daftar menunggu.
     */
    public function displayData()
    {
        $today = now()->toDateString();

        // Nomor antrian yang paling terakhir dipanggil (status Dipanggil)
        $dipanggil = Antrian::with(['pasien', 'rekamMedis'])
            ->where('tanggal', $today)
            ->where('status', 'Dipanggil')
            ->orderBy('updated_at', 'desc')
            ->first();

        $total    = Antrian::where('tanggal', $today)->count();
        $selesai  = Antrian::where('tanggal', $today)->where('status', 'Selesai')->count();
        $menunggu = Antrian::where('tanggal', $today)
            ->whereIn('status', ['Menunggu', 'Dipanggil'])
            ->count();

        // Daftar antrian yang masih menunggu / baru dipanggil (sertakan poli jika ada)
        $daftarMenunggu = Antrian::with(['pasien', 'rekamMedis'])
            ->where('tanggal', $today)
            ->whereIn('status', ['Menunggu', 'Dipanggil'])
            ->orderByRaw("CASE WHEN LOWER(status)='dipanggil' THEN 0 ELSE 1 END")
            ->orderBy('no_antrian', 'asc')
            ->get()
            ->map(fn($a) => [
                'no_antrian' => str_pad($a->no_antrian, 3, '0', STR_PAD_LEFT),
                'nama'       => $a->pasien->nama ?? '-',
                'status'     => $a->status,
                'poli'       => $a->rekamMedis->pelayanan_kesehatan ?? null,
            ]);

        return response()->json([
            'dilayani'        => $dipanggil ? [
                'no_antrian'  => str_pad($dipanggil->no_antrian, 3, '0', STR_PAD_LEFT),
                'nama'        => $dipanggil->pasien->nama ?? '-',
                'jenis'       => $dipanggil->jenis,
                'poli'        => $dipanggil->rekamMedis->pelayanan_kesehatan ?? null,
            ] : null,
            'total'           => $total,
            'selesai'         => $selesai,
            'menunggu'        => $menunggu,
            'daftar_menunggu' => $daftarMenunggu,
        ]);
    }

    public function realtimeData()
    {
        $antrians = Antrian::with(['pasien', 'rekamMedis'])
            ->where('tanggal', now()->toDateString())
            ->orderByRaw("
                CASE
                  WHEN LOWER(status) = 'dipanggil' AND NOT EXISTS (SELECT 1 FROM rekam_medis WHERE rekam_medis.antrian_id = antrian.id AND rekam_medis.deleted_at IS NULL) THEN 0
                  WHEN LOWER(status) = 'menunggu' THEN 1
                  WHEN LOWER(status) = 'dipanggil' AND EXISTS (SELECT 1 FROM rekam_medis WHERE rekam_medis.antrian_id = antrian.id AND rekam_medis.deleted_at IS NULL) THEN 2
                  WHEN LOWER(status) = 'dilayani' THEN 3
                  WHEN LOWER(status) IN ('selesai','batal') THEN 4
                  ELSE 5
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
                if ($a->rekamMedis) {
                    $statusHtml = '<span class="status-badge s-menunggu-dokter">Menunggu Dokter</span>';
                } else {
                    $statusHtml = '<span class="status-badge s-menunggu-ttv">Menunggu TTV</span>';
                }
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
                    $buttonsHtml .= '<button type="button" class="btn-panggil" onclick="panggilStatusLangsung(' . $a->id . ', \'' . addslashes($a->pasien->nama ?? '') . '\')" title="Panggil Pasien"><i class="fas fa-bullhorn text-xs"></i> Panggil</button>';
                } elseif ($st === 'dipanggil') {
                    if (!$a->rekamMedis) {
                        $buttonsHtml .= '<button type="button" class="btn-ttv" onclick="openPanggil(' . $a->id . ', \'' . addslashes($a->pasien->nama ?? '') . '\')" title="Pemeriksaan Awal TTV"><i class="fas fa-notes-medical text-xs"></i> Pemeriksaan Awal</button>';
                        $buttonsHtml .= '<button type="button" class="btn-panggil" onclick="panggilStatusLangsung(' . $a->id . ', \'' . addslashes($a->pasien->nama ?? '') . '\')" title="Panggil Ulang Pasien"><i class="fas fa-redo text-xs"></i> Panggil Ulang</button>';
                    }
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

    // Update status antrian
    public function updateStatus(Request $request, $id)
    {
        $antrian = Antrian::with(['pasien', 'rekamMedis'])->findOrFail($id);

        $request->validate([
            'status' => 'required|in:Menunggu,Terpanggil,Dipanggil,Dilayani,Selesai,Batal',
        ]);

        $antrian->update(['status' => $request->status]);

        $message = 'Status antrian berhasil diupdate.';

        if ($request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => $message,
                'no_antrian'  => str_pad($antrian->no_antrian, 3, '0', STR_PAD_LEFT),
                'nama'        => $antrian->pasien->nama ?? '',
                'poli'        => $antrian->rekamMedis->pelayanan_kesehatan ?? null,
                'antrian'     => $antrian,
            ]);
        }

        return redirect()->route('admin.pemesanan')->with('success', $message);
    }

    public function showRiwayatDetail($id)
    {
        $user = auth()->user();
        $pasien = $user->pasien ?? null;

        if (!$pasien) {
            abort(403, 'Profil pasien tidak ditemukan.');
        }

        $antrian = Antrian::with([
            'rekamMedis.dokter',
            'rekamMedis.diagnosa.icdx',
            'rekamMedis.resep.details.obat',
            'rekamMedis.billing.details',
            'rekamMedis.feedback',
        ])->findOrFail($id);

        // Security check
        if ($antrian->pasien_id !== $pasien->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data ini.');
        }

        // Normalisasi data terbalik (Self-healing layer)
        $rm = $antrian->rekamMedis;
        $layananRaw = $rm ? ($rm->pelayanan_kesehatan ?? 'Poli Umum') : 'Poli Umum';
        $jenisRaw = $rm ? ($rm->jenis_pelayanan ?? 'Umum') : 'Umum';

        $layananUpper = strtoupper(trim($layananRaw));
        $jenisUpper = strtoupper(trim($jenisRaw));

        $isLayananPayment = in_array($layananUpper, ['UMUM', 'BPJS']);
        $isJenisPoli = str_contains($jenisUpper, 'POLI') || str_contains($jenisUpper, 'UGD') || str_contains($jenisUpper, 'LAB') || str_contains($jenisUpper, 'SPA');

        if ($isLayananPayment || $isJenisPoli) {
            $temp = $layananRaw;
            $layananRaw = $jenisRaw;
            $jenisRaw = $temp;

            $layananUpper = strtoupper(trim($layananRaw));
            $jenisUpper = strtoupper(trim($jenisRaw));
        }

        if (in_array($jenisUpper, ['UMUM', 'BPJS'])) {
            $jenisPelayanan = ($jenisUpper === 'BPJS') ? 'BPJS' : 'Umum';
        } else {
            $jenisPelayanan = 'Umum';
        }

        if (str_contains($layananUpper, 'GIGI')) {
            $layanan = 'Poli Gigi';
        } elseif (str_contains($layananUpper, 'KIA') || str_contains($layananUpper, 'KB')) {
            $layanan = 'Poli KIA';
        } elseif (str_contains($layananUpper, 'UGD') || str_contains($layananUpper, 'DARURAT')) {
            $layanan = 'UGD';
        } elseif (str_contains($layananUpper, 'LAB')) {
            $layanan = 'Laboratorium';
        } elseif (str_contains($layananUpper, 'SPA')) {
            $layanan = 'Baby Spa';
        } else {
            $layanan = 'Poli Umum';
        }

        return view('dashboard_pasien_detail', compact('antrian', 'pasien', 'layanan', 'jenisPelayanan'));
    }
}