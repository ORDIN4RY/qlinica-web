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
use App\Models\Pasien;
class DokterController extends Controller
{
    public function dashboard()
    {
        $pegawai = auth()->user()->pegawai;

        $totalRekamMedis = RekamMedis::where('dokter_id', $pegawai->id)->count();

        // Hitung antrian hari ini khusus untuk dokter yang sedang login
        // Filter melalui rekam_medis karena antrian tidak punya kolom dokter_id
        $antrianHariIni = \App\Models\Antrian::where('tanggal', now()->toDateString())
            ->whereHas('rekamMedis', function ($q) use ($pegawai) {
                $q->where('dokter_id', $pegawai->id);
            })
            ->count();

        $selesaiHariIni = \App\Models\Antrian::where('tanggal', now()->toDateString())
            ->whereHas('rekamMedis', function ($q) use ($pegawai) {
                $q->where('dokter_id', $pegawai->id);
            })
            ->where('status', 'Selesai')
            ->count();

        return view('dokter.dashboard', compact(
            'totalRekamMedis',
            'antrianHariIni',
            'selesaiHariIni'
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

    public function antrianIndex(\Illuminate\Http\Request $request)
    {
        $user    = auth()->user();
        $pegawai = $user->pegawai;
        $hasAll  = $user->hasMenuAccess('Antrian Pemeriksaan', 'all');

        // Urutan sort: default = terbaru di atas, batal di bawah
        $sortBy = $request->query('sort', 'default');

        $query = Antrian::with(['pasien', 'rekamMedis'])
            ->where('tanggal', now()->toDateString());

        // Filter: hanya antrian yang ditugaskan ke dokter ini,
        // kecuali dokter memiliki akses 'Antrian Pemeriksaan > all'
        if (!$hasAll && $pegawai) {
            $query->whereHas('rekamMedis', function ($q) use ($pegawai) {
                $q->where('dokter_id', $pegawai->id);
            });
        }

        // Urutkan berdasarkan pilihan sort
        match ($sortBy) {
            // Paling baru di atas (by ID desc), batal di bawah
            'default' => $query
                ->orderByRaw("
                    CASE status
                        WHEN 'Dipanggil' THEN 1
                        WHEN 'Menunggu'  THEN 2
                        WHEN 'Selesai'   THEN 3
                        WHEN 'Batal'     THEN 4
                        ELSE 5
                    END ASC
                ")
                ->orderByDesc('id'),

            // Nomor antrian terkecil duluan
            'nomor_asc'  => $query->orderBy('no_antrian', 'asc'),
            'nomor_desc' => $query->orderBy('no_antrian', 'desc'),

            // Berdasarkan status alfabet
            'status_asc'  => $query->orderBy('status', 'asc')->orderByDesc('id'),
            'status_desc' => $query->orderBy('status', 'desc')->orderByDesc('id'),

            // Nama pasien
            'nama_asc'  => $query->join('pasien', 'antrian.pasien_id', '=', 'pasien.id')
                ->orderBy('pasien.nama', 'asc')->select('antrian.*'),
            'nama_desc' => $query->join('pasien', 'antrian.pasien_id', '=', 'pasien.id')
                ->orderBy('pasien.nama', 'desc')->select('antrian.*'),

            default => $query->orderByRaw("
                    CASE status
                        WHEN 'Dipanggil' THEN 1
                        WHEN 'Menunggu'  THEN 2
                        WHEN 'Selesai'   THEN 3
                        WHEN 'Batal'     THEN 4
                        ELSE 5
                    END ASC
                ")->orderByDesc('id'),
        };

        $antrians = $query->get();

        return view('dokter.antrian', [
            'antrians'      => $antrians,
            'jumlahAntrian' => $antrians->count(),
            'Dipanggil'     => $antrians->where('status', 'Dipanggil')->count(),
            'selesai'       => $antrians->where('status', 'Selesai')->count(),
            'sortBy'        => $sortBy,
            'hasAll'        => $hasAll,
        ]);
    }

    public function pasienIndex(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');

        // Tampilkan SEMUA pasien terdaftar (read-only untuk dokter)
        $query = \App\Models\Pasien::with(['agama', 'pendidikan', 'pekerjaan'])
            ->selectRaw('pasien.*, TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) as umur')
            ->orderBy('nama');

        if ($q) {
            $query->where(function($qb) use ($q) {
                $qb->where('nama', 'like', "%{$q}%")
                   ->orWhere('no_rm', 'like', "%{$q}%")
                   ->orWhere('nik', 'like', "%{$q}%");
            });
        }

        $pasiens = $query->paginate(15)->withQueryString();

        return view('dokter.pasien', compact('pasiens'));
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
        $isAdmin = auth()->user()->role === 'admin';

        // Validasi bahwa antrian ini memang ditugaskan ke dokter yang sedang login
        // Admin diperbolehkan membypass validasi ini untuk keperluan pengujian/supervisi
        if (!$isAdmin) {
            if (!$pegawai) {
                return redirect()->route('dokter.antrian')->with('error', 'Anda tidak memiliki akses untuk mendiagnosa pasien ini.');
            }
            if ($antrian->rekamMedis && $antrian->rekamMedis->dokter_id != $pegawai->id) {
                return redirect()->route('dokter.antrian')->with('error', 'Anda tidak memiliki akses untuk mendiagnosa pasien ini.');
            }
        }

        // Validasi bahwa antrian sedang dalam status Dipanggil atau Selesai
        if (!in_array($antrian->status, ['Dipanggil', 'Selesai'])) {
            return redirect()->route('dokter.antrian')->with('error', 'Pasien harus dalam status Dipanggil untuk memberikan diagnosa.');
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
            'is_rekomendasi_rawat_inap' => 'nullable|boolean',
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
            'riwayat_alergi' => 'nullable|string|max:1000',
            'pakai_resep' => 'required|in:Ya,Tidak',
            // Gunakan exclude_unless agar seluruh validasi resep diabaikan saat pakai_resep = Tidak
            'obat_id' => 'exclude_unless:pakai_resep,Ya|array|min:1',
            'obat_id.*' => 'exclude_unless:pakai_resep,Ya|required|exists:obat,id',
            'jumlah' => 'exclude_unless:pakai_resep,Ya|array|min:1',
            'jumlah.*' => 'exclude_unless:pakai_resep,Ya|required|integer|min:1',
            'dosis' => 'exclude_unless:pakai_resep,Ya|nullable|array',
            'aturan_pakai' => 'exclude_unless:pakai_resep,Ya|nullable|array',
            'keterangan' => 'exclude_unless:pakai_resep,Ya|nullable|array',
        ]);

        try {
            DB::transaction(function () use ($antrian, $pegawai, $request) {
                // Buat atau update rekam medis
                $rekamMedis = RekamMedis::updateOrCreate(
                    ['antrian_id' => $antrian->id],
                    [
                        'pasien_id' => $antrian->pasien_id,
                        'dokter_id' => $pegawai ? $pegawai->id : ($antrian->rekamMedis ? $antrian->rekamMedis->dokter_id : 1),
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
                        'is_rekomendasi_rawat_inap' => $request->has('is_rekomendasi_rawat_inap'),
                        'rujukan_ke' => $request->rujukan_ke,
                        'catatan' => $request->catatan,
                        'kasus_penyakit' => $request->kasus_penyakit,
                        'pelayanan_kesehatan' => $request->pelayanan_kesehatan,
                        'status_pasien' => $request->status_pasien,
                        'jenis_pelayanan' => $request->jenis_pelayanan,
                        'pengobatan' => $request->pengobatan,
                    ]
                );

                if ($request->has('riwayat_alergi')) {
                    $antrian->pasien->update(['riwayat_alergi' => $request->riwayat_alergi]);
                }

                // Hapus diagnosa lama terlebih dahulu untuk menghindari UNIQUE constraint error
                RekamMedisDiagnosa::where('rekam_medis_id', $rekamMedis->id)->delete();

                // Simpan diagnosa baru
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

                    // Hapus resep lama jika sudah ada
                    if ($rekamMedis->resep) {
                        if (in_array($rekamMedis->resep->status, ['Menunggu', 'Menunggu Pembayaran'])) {
                            $rekamMedis->resep->details()->delete();
                            $rekamMedis->resep->delete();

                            // Bersihkan tagihan obat lama yang sudah ada di billing
                            $billing = \App\Models\Billing::where('rekam_medis_id', $rekamMedis->id)->first();
                            if ($billing && $billing->status === 'Belum Bayar') {
                                \App\Models\BillingDetail::where('billing_id', $billing->id)
                                    ->where('kategori', 'Obat')
                                    ->delete();

                                $billing->update([
                                    'biaya_obat' => 0.00,
                                ]);
                            }
                        } else {
                            throw new \Exception("Resep sudah diproses/dibayar oleh apotek dan tidak dapat diubah.");
                        }
                    }

                    $resep = Resep::create([
                        'rekam_medis_id' => $rekamMedis->id,
                        'dokter_id' => $pegawai ? $pegawai->id : $rekamMedis->dokter_id,
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
                } else {
                    // JIKA DOKTER MEMILIH "Tidak" pakai resep, hapus resep lama (jika ada) dan biaya obat di billing
                    if ($rekamMedis->resep) {
                        if (in_array($rekamMedis->resep->status, ['Menunggu', 'Menunggu Pembayaran'])) {
                            $rekamMedis->resep->details()->delete();
                            $rekamMedis->resep->delete();

                            $billing = \App\Models\Billing::where('rekam_medis_id', $rekamMedis->id)->first();
                            if ($billing && $billing->status === 'Belum Bayar') {
                                \App\Models\BillingDetail::where('billing_id', $billing->id)
                                    ->where('kategori', 'Obat')
                                    ->delete();

                                $billing->update([
                                    'biaya_obat' => 0.00,
                                ]);
                            }
                        } else {
                            throw new \Exception("Resep sudah diproses/dibayar oleh apotek dan tidak dapat diubah.");
                        }
                    }
                }

                // Buat tagihan billing otomatis untuk kunjungan pasien ini
                $noInvoice = 'INV-' . now()->format('Ymd') . '-' . str_pad($rekamMedis->id, 4, '0', STR_PAD_LEFT);
                
                // Cek apakah billing sudah ada (jika re-submit diagnosa)
                $billing = \App\Models\Billing::where('rekam_medis_id', $rekamMedis->id)->first();
                if (!$billing) {
                    $billing = \App\Models\Billing::create([
                        'rekam_medis_id' => $rekamMedis->id,
                        'pasien_id' => $rekamMedis->pasien_id,
                        'no_invoice' => $noInvoice,
                        'biaya_registrasi' => 50000.00,
                        'biaya_tindakan' => $request->tindakan ? 75000.00 : 0.00,
                        'biaya_obat' => 0.00,
                        'grand_total' => 50000.00 + ($request->tindakan ? 75000.00 : 0.00),
                        'status' => 'Belum Bayar',
                    ]);

                    // Tambahkan rincian tagihan
                    \App\Models\BillingDetail::create([
                        'billing_id' => $billing->id,
                        'nama_item' => 'Registrasi & Jasa Konsultasi Dokter',
                        'kategori' => 'Registrasi',
                        'jumlah' => 1,
                        'harga_satuan' => 50000.00,
                        'subtotal' => 50000.00,
                    ]);

                    if ($request->tindakan) {
                        \App\Models\BillingDetail::create([
                            'billing_id' => $billing->id,
                            'nama_item' => 'Tindakan Medis: ' . substr($request->tindakan, 0, 100),
                            'kategori' => 'Tindakan',
                            'jumlah' => 1,
                            'harga_satuan' => 75000.00,
                            'subtotal' => 75000.00,
                        ]);
                    }

                    $billing->recalculateTotals();
                    $billing->save();
                } else {
                    // Jika billing sudah ada tapi belum lunas, kita update biaya tindakan jika ada perubahan tindakan
                    if ($billing->status === 'Belum Bayar') {
                        $biayaTindakan = $request->tindakan ? 75000.00 : 0.00;
                        
                        // Hapus detail tindakan lama jika ada
                        \App\Models\BillingDetail::where('billing_id', $billing->id)
                            ->where('kategori', 'Tindakan')
                            ->delete();

                        if ($request->tindakan) {
                            \App\Models\BillingDetail::create([
                                'billing_id' => $billing->id,
                                'nama_item' => 'Tindakan Medis: ' . substr($request->tindakan, 0, 100),
                                'kategori' => 'Tindakan',
                                'jumlah' => 1,
                                'harga_satuan' => 75000.00,
                                'subtotal' => 75000.00,
                            ]);
                        }

                        $billing->update([
                            'biaya_tindakan' => $biayaTindakan,
                        ]);
                        $billing->recalculateTotals();
                        $billing->save();
                    }
                }

                // Update status antrian menjadi Selesai
                $antrian->update(['status' => 'Selesai']);
            });
        } catch (\Exception $e) {
            return redirect()->route('dokter.antrian')
                ->with('error', 'Gagal menyimpan diagnosa: ' . $e->getMessage());
        }

        return redirect()->route('dokter.antrian')->with('success', 'Diagnosa dan resep berhasil disimpan. Pasien telah selesai dilayani.');
    }
    public function showPasien($id)
    {
        $pasien = Pasien::with(['agama', 'pendidikan', 'pekerjaan'])->findOrFail($id);
        
        // Ambil riwayat kunjungan (Rekam Medis) pasien ini
        $riwayatMedis = RekamMedis::with(['dokter', 'antrian', 'diagnosa.icdx', 'resep.details.obat'])
            ->where('pasien_id', $pasien->id)
            ->orderByDesc('tanggal_periksa')
            ->get();

        return view('dokter.pasien_show', compact('pasien', 'riwayatMedis'));
    }

    public function periksa($id)
    {
        $antrian = Antrian::with(['pasien', 'rekamMedis'])->findOrFail($id);
        $pegawai = auth()->user()->pegawai;

        $isAdmin = auth()->user()->role === 'admin';

        // Validasi bahwa antrian ini memang ditugaskan ke dokter yang sedang login
        // Admin diperbolehkan membypass validasi ini untuk keperluan pengujian/supervisi
        if (!$isAdmin) {
            if (!$pegawai) {
                return redirect()->route('dokter.antrian')->with('error', 'Anda tidak memiliki akses untuk memeriksa pasien ini.');
            }
            if ($antrian->rekamMedis && $antrian->rekamMedis->dokter_id != $pegawai->id) {
                return redirect()->route('dokter.antrian')->with('error', 'Anda tidak memiliki akses untuk memeriksa pasien ini.');
            }
        }

        if (!in_array($antrian->status, ['Dipanggil', 'Selesai'])) {
            return redirect()->route('dokter.antrian')->with('error', 'Pasien harus dalam status Dipanggil untuk memulai pemeriksaan.');
        }

        $obats = Obat::orderBy('nama')->get();

        return view('dokter.periksa', compact('antrian', 'obats'));
    }
}
