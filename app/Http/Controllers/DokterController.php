<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RekamMedis;
use App\Models\RekamMedisDiagnosa;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\Obat;
use App\Models\Antrian;
use App\Models\Icdx;

class DokterController extends Controller
{
    public function dashboard()
    {
        $pegawai = auth()->user()->pegawai;

        $totalRekamMedis = RekamMedis::where('dokter_id', $pegawai->id)->count();
        $totalResep = Resep::whereHas('rekamMedis', fn($q) => $q->where('dokter_id', $pegawai->id))->count();
        $resepPending = Resep::whereHas('rekamMedis', fn($q) => $q->where('dokter_id', $pegawai->id))
            ->where('status', 'Menunggu')
            ->count();
        $resepSelesai = Resep::whereHas('rekamMedis', fn($q) => $q->where('dokter_id', $pegawai->id))
            ->where('status', 'Selesai')
            ->count();

        return view('dokter.dashboard', compact(
            'totalRekamMedis',
            'totalResep',
            'resepPending',
            'resepSelesai'
        ));
    }

    public function resepIndex(Request $request)
    {
        $pegawai = auth()->user()->pegawai;
        $search = $request->query('search');

        $query = RekamMedis::with(['pasien', 'resep'])
            ->where('dokter_id', $pegawai->id)
            ->orderByDesc('tanggal_periksa');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('pasien', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%")
                       ->orWhere('no_rm', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $rekamMedis = $query->paginate(12)->withQueryString();

        return view('dokter.resep', compact('rekamMedis', 'search'));
    }

    public function createResep(RekamMedis $rekamMedis)
    {
        $pegawai = auth()->user()->pegawai;

        if ($rekamMedis->dokter_id !== $pegawai->id) {
            return redirect()->route('dokter.resep.index')->with('error', 'Anda tidak dapat membuat resep untuk rekam medis pasien lain.');
        }

        if ($rekamMedis->resep) {
            return redirect()->route('dokter.resep.index')->with('error', 'Resep untuk rekam medis ini sudah dibuat.');
        }

        $obats = Obat::orderBy('nama')->get();

        return view('dokter.resep-form', compact('rekamMedis', 'obats'));
    }

    public function storeResep(Request $request, RekamMedis $rekamMedis)
    {
        $pegawai = auth()->user()->pegawai;

        if ($rekamMedis->dokter_id !== $pegawai->id) {
            return redirect()->route('dokter.resep.index')->with('error', 'Anda tidak dapat membuat resep untuk rekam medis pasien lain.');
        }

        if ($rekamMedis->resep) {
            return redirect()->route('dokter.resep.index')->with('error', 'Resep untuk rekam medis ini sudah dibuat.');
        }

        $request->validate([
            'catatan_dokter' => 'nullable|string|max:1000',
            'obat_id' => 'required|array|min:1',
            'obat_id.*' => 'required|exists:obat,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',
            'dosis' => 'nullable|array',
            'aturan_pakai' => 'nullable|array',
            'keterangan' => 'nullable|array',
        ]);

        $obatIds = $request->input('obat_id');
        $jumlah = $request->input('jumlah');
        $dosis = $request->input('dosis', []);
        $aturanPakai = $request->input('aturan_pakai', []);
        $keterangan = $request->input('keterangan', []);

        if (count($obatIds) !== count($jumlah)) {
            return redirect()->back()->withInput()->with('error', 'Jumlah obat dan dosis tidak cocok.');
        }

        DB::transaction(function () use ($rekamMedis, $obatIds, $jumlah, $dosis, $aturanPakai, $keterangan, $request) {
            $resep = Resep::create([
                'rekam_medis_id' => $rekamMedis->id,
                'dokter_id' => $rekamMedis->dokter_id,
                'status' => 'Menunggu',
                'catatan_dokter' => $request->input('catatan_dokter') ?: $rekamMedis->catatan,
            ]);

            foreach ($obatIds as $index => $obatId) {
                ResepDetail::create([
                    'resep_id' => $resep->id,
                    'obat_id' => $obatId,
                    'jumlah' => $jumlah[$index],
                    'dosis' => $dosis[$index] ?? null,
                    'aturan_pakai' => $aturanPakai[$index] ?? null,
                    'keterangan' => $keterangan[$index] ?? null,
                ]);
            }
        });

        return redirect()->route('dokter.resep.index')->with('success', 'Resep berhasil dikirim ke apoteker.');
    }

    public function antrianIndex()
    {
        $antrians = Antrian::with(['pasien', 'rekamMedis'])
            ->where('tanggal', now()->toDateString())
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.antrian', [
            'antrians' => $antrians,
            'jumlahAntrian' => $antrians->count(),
            'Dipanggil' => $antrians->where('status', 'Dipanggil')->count(),
            'selesai' => $antrians->where('status', 'Selesai')->count(),
        ]);
    }

    public function panggilAntrian(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Dipanggil,Dilayani,Selesai',
        ]);

        $antrian->update(['status' => $request->status]);

        $message = match ($request->status) {
            'Dipanggil' => 'Pasien dipanggil ke ruang dokter.',
            'Dilayani' => 'Pasien sedang dilayani.',
            'Selesai' => 'Pelayanan pasien selesai.',
        };

        return redirect()->route('dokter.antrian')->with('success', $message);
    }

    public function simpanDiagnosa(Request $request, $antrianId)
    {
        $antrian = Antrian::findOrFail($antrianId);
        $pegawai = auth()->user()->pegawai;

        // Validasi bahwa antrian sedang dalam status Dilayani
        if ($antrian->status !== 'Dilayani') {
            return redirect()->route('dokter.antrian')->with('error', 'Pasien harus dalam status dilayani untuk memberikan diagnosa.');
        }

        $request->validate([
            'anamnesis' => 'nullable|string|max:1000',
            'pemeriksaan_fisik' => 'nullable|string|max:1000',
            'tekanan_darah' => 'nullable|string|max:20',
            'suhu' => 'nullable|numeric|min:30|max:45',
            'berat_badan' => 'nullable|numeric|min:1|max:200',
            'tinggi_badan' => 'nullable|numeric|min:30|max:250',
            'nadi' => 'nullable|integer|min:40|max:200',
            'respirasi' => 'nullable|integer|min:10|max:60',
            'tindakan' => 'nullable|string|max:1000',
            'prognosa' => 'nullable|string|max:500',
            'keadaan_keluar' => 'nullable|string|max:100',
            'rujukan_ke' => 'nullable|string|max:200',
            'catatan' => 'nullable|string|max:1000',
            'diagnosa' => 'required|array|min:1',
            'diagnosa.*' => 'required|exists:icdx,id',
            'diagnosa_primer' => 'required|exists:icdx,id',
            'catatan_dokter' => 'nullable|string|max:1000',
            'kasus_penyakit' => 'required|string|max:100',
            'pelayanan_kesehatan' => 'nullable|string|max:100',
            'status_pasien' => 'nullable|string|max:100',
            'jenis_pelayanan' => 'nullable|string|max:100',
            'pengobatan' => 'nullable|string|max:1000',
            'pakai_resep' => 'required|in:Ya,Tidak',
            'obat_id' => 'required_if:pakai_resep,Ya|array',
            'obat_id.*' => 'required_if:pakai_resep,Ya|exists:obat,id',
            'jumlah' => 'required_if:pakai_resep,Ya|array',
            'jumlah.*' => 'required_if:pakai_resep,Ya|integer|min:1',
            'dosis' => 'nullable|array',
            'aturan_pakai' => 'nullable|array',
            'keterangan' => 'nullable|array',
        ]);

        DB::transaction(function () use ($antrian, $pegawai, $request) {
            // Buat rekam medis
            $rekamMedis = RekamMedis::updateOrCreate(
                ['antrian_id' => $antrian->id],
                [
                    'pasien_id' => $antrian->pasien_id,
                    'dokter_id' => $pegawai->id,
                    'tanggal_periksa' => now(),
                    'anamnesis' => $request->anamnesis,
                    'pemeriksaan_fisik' => $request->pemeriksaan_fisik,
                    'tekanan_darah' => $request->tekanan_darah,
                    'suhu' => $request->suhu,
                    'berat_badan' => $request->berat_badan,
                    'tinggi_badan' => $request->tinggi_badan,
                    'nadi' => $request->nadi,
                    'respirasi' => $request->respirasi,
                    'tindakan' => $request->tindakan,
                    'prognosa' => $request->prognosa,
                    'keadaan_keluar' => $request->keadaan_keluar,
                    'rujukan_ke' => $request->rujukan_ke,
                    'catatan' => $request->catatan,
                    'kasus_penyakit' => $request->kasus_penyakit,
                    'pelayanan_kesehatan' => $request->pelayanan_kesehatan,
                    'status_pasien' => $request->status_pasien,
                    'jenis_pelayanan' => $request->jenis_pelayanan,
                    'pengobatan' => $request->pengobatan,
                ]
            );

            // Simpan diagnosa
            foreach ($request->diagnosa as $icdxId) {
                RekamMedisDiagnosa::create([
                    'rekam_medis_id' => $rekamMedis->id,
                    'icdx_id' => $icdxId,
                    'is_primer' => $icdxId == $request->diagnosa_primer,
                ]);
            }

            // Simpan resep jika ada obat dan pilihan "Ya"
            if ($request->pakai_resep === 'Ya' && $request->has('obat_id') && !empty($request->obat_id)) {
                $obatIds = $request->obat_id;
                $jumlah = $request->jumlah;
                $dosis = $request->dosis ?? [];
                $aturanPakai = $request->aturan_pakai ?? [];
                $keterangan = $request->keterangan ?? [];

                $resep = Resep::create([
                    'rekam_medis_id' => $rekamMedis->id,
                    'dokter_id' => $pegawai->id,
                    'status' => 'Menunggu',
                    'catatan_dokter' => $request->catatan_dokter ?: $rekamMedis->catatan,
                ]);

                foreach ($obatIds as $index => $obatId) {
                    ResepDetail::create([
                        'resep_id' => $resep->id,
                        'obat_id' => $obatId,
                        'jumlah' => $jumlah[$index],
                        'dosis' => $dosis[$index] ?? null,
                        'aturan_pakai' => $aturanPakai[$index] ?? null,
                        'keterangan' => $keterangan[$index] ?? null,
                    ]);
                }
            }

            // Update status antrian menjadi Selesai
            $antrian->update(['status' => 'Selesai']);
        });

        return redirect()->route('dokter.antrian')->with('success', 'Diagnosa dan resep berhasil disimpan. Pasien telah selesai dilayani.');
    }
}
