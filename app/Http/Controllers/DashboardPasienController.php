<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Pasien;
use App\Models\RekamMedis;
use App\Models\Resep;
use App\Models\Billing;
use App\Models\RawatInap;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardPasienController extends Controller
{
    /**
     * Landing page portal pasien dengan data lengkap dan dinamis.
     */
    public function portal()
    {
        $user = auth()->user();
        $pasien = $user->pasien;

        if (!$pasien) {
            return redirect('/login')->with('error', 'Data pasien tidak ditemukan');
        }

        // ===== 1. DATA ANTRIAN HARI INI =====
        $today = Carbon::today();
        
        // Antrian aktif pasien (belum selesai)
        $antrianAktif = Antrian::where('pasien_id', $pasien->id)
            ->where('tanggal', $today)
            ->whereNotIn('status', ['Selesai', 'Batal'])
            ->first();

        // Antrian sedang dilayani hari ini
        $antrianDilayani = Antrian::where('tanggal', $today)
            ->where('status', 'Dipanggil')
            ->orderBy('no_antrian')
            ->first();

        // Total antrian hari ini
        $totalAntrianHariIni = Antrian::where('tanggal', $today)->count();

        // Antrian yang sudah selesai hari ini
        $antrianSelesai = Antrian::where('tanggal', $today)
            ->where('status', 'Selesai')
            ->count();

        // Antrian yang masih menunggu hari ini (belum dipanggil)
        $antrianMenunggu = Antrian::where('tanggal', $today)
            ->whereNotIn('status', ['Selesai', 'Batal', 'Dipanggil'])
            ->count();

        // Total antrian untuk pasien ini yang menunggu hari ini
        $antrianPasienMenunggu = Antrian::where('pasien_id', $pasien->id)
            ->where('tanggal', $today)
            ->whereNotIn('status', ['Selesai', 'Batal', 'Dipanggil'])
            ->count();

        // Total pasien yang sudah selesai hari ini
        $pasienSelesaiHariIni = Antrian::where('tanggal', $today)
            ->where('status', 'Selesai')
            ->distinct('pasien_id')
            ->count('pasien_id');

        // ===== 2. RIWAYAT ANTRIAN (HISTORY) =====
        $riwayatAntrian = Antrian::with([
            'pasien',
            'rekamMedis.dokter',
            'rekamMedis.diagnosa.icdx',
            'rekamMedis.resep.details.obat',
            'rekamMedis.feedback',
        ])
            ->where('pasien_id', $pasien->id)
            ->where(function ($query) use ($today) {
                $query->where('tanggal', '<', $today)
                    ->orWhereIn('status', ['Selesai', 'Batal']);
            })
            ->latest('tanggal')
            ->limit(50)
            ->get();

        // ===== 3. TOTAL KUNJUNGAN (dari tabel antrian) =====
        $totalKunjungan = Antrian::where('pasien_id', $pasien->id)
            ->where('status', 'Selesai')
            ->count();

        // ===== 4. RIWAYAT DETAIL (untuk tab riwayat) =====
        $riwayatDetail = RekamMedis::with(['dokter.user', 'diagnosa', 'resep'])
            ->where('pasien_id', $pasien->id)
            ->latest('tanggal_periksa')
            ->limit(20)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'tanggal' => $record->tanggal_periksa->format('d M Y H:i'),
                    'dokter' => $record->dokter?->user?->nama ?? 'Dokter Klinik',
                    'keluhan' => $record->keluhan ?? '-',
                    'diagnosa' => $record->diagnosa?->map(fn($d) => $d->diagnosa)->join(', ') ?? '-',
                    'resep' => $record->resep ? 'Ada Resep' : 'Tidak Ada Resep',
                ];
            });

        // ===== 5. BIAYA BERJALAN / TAGIHAN AKTIF =====
        $biayaBerlangsung = collect();

        // Cek Rawat Inap Aktif
        $rawatInapAktif = RawatInap::where('pasien_id', $pasien->id)
            ->whereNull('tgl_keluar')
            ->first();

        if ($rawatInapAktif) {
            $billing = Billing::where('pasien_id', $pasien->id)
                ->where('rawat_inap_id', $rawatInapAktif->id)
                ->where('status', 'Belum Bayar')
                ->first();

            if ($billing) {
                $billing->recalculateTotals();
                $biayaBerlangsung->push([
                    'tipe' => 'Rawat Inap',
                    'no_invoice' => $billing->no_invoice,
                    'grand_total' => $billing->grand_total,
                    'tgl_mulai' => $rawatInapAktif->tgl_masuk->format('Y-m-d H:i:s'),
                    'kamar' => $rawatInapAktif->kamar?->nama_kamar ?? 'Kamar',
                    'status' => 'Dirawat',
                    'biaya_registrasi' => $billing->biaya_registrasi,
                    'biaya_kamar' => $billing->biaya_kamar,
                    'biaya_tindakan' => $billing->biaya_tindakan,
                    'biaya_obat' => $billing->biaya_obat,
                    'potongan_bpjs' => $billing->potongan_bpjs,
                    'no_bpjs' => $billing->no_bpjs,
                    'jenis_penjamin' => $rawatInapAktif->jenis_penjamin,
                    'details' => $billing->details->map(fn($d) => [
                        'nama_item' => $d->nama_item,
                        'kategori' => $d->kategori,
                        'jumlah' => $d->jumlah,
                        'harga_satuan' => $d->harga_satuan,
                        'subtotal' => $d->subtotal,
                    ])->toArray(),
                ]);
            }

            // Ambil resep berjalan untuk rawat inap
            $resepRawatInap = Resep::where('rawat_inap_id', $rawatInapAktif->id)
                ->whereIn('status', ['Menunggu', 'Diproses', 'Selesai'])
                ->with(['details.obat', 'dokter'])
                ->get();
        }

        // Cek Pelayanan Klinik Aktif (Rawat Jalan)
        $billingRawatJalan = Billing::with(['rekamMedis.dokter.user', 'rekamMedis.resep.details.obat', 'details'])
            ->where('pasien_id', $pasien->id)
            ->where('status', 'Belum Bayar')
            ->whereNull('rawat_inap_id')
            ->get();

        $resepBerjalan = collect();

        foreach ($billingRawatJalan as $bj) {
            $bj->recalculateTotals();
            $biayaBerlangsung->push([
                'tipe' => 'Rawat Jalan',
                'no_invoice' => $bj->no_invoice,
                'grand_total' => $bj->grand_total,
                'tgl_mulai' => $bj->rekamMedis ? $bj->rekamMedis->tanggal_periksa->format('Y-m-d H:i:s') : $bj->created_at->format('Y-m-d H:i:s'),
                'kamar' => null,
                'status' => 'Pelayanan',
                'biaya_registrasi' => $bj->biaya_registrasi,
                'biaya_kamar' => $bj->biaya_kamar,
                'biaya_tindakan' => $bj->biaya_tindakan,
                'biaya_obat' => $bj->biaya_obat,
                'potongan_bpjs' => $bj->potongan_bpjs,
                'no_bpjs' => $bj->no_bpjs,
                'jenis_penjamin' => $bj->no_bpjs ? 'BPJS KESEHATAN' : 'Umum',
                'details' => $bj->details->map(fn($d) => [
                    'nama_item' => $d->nama_item,
                    'kategori' => $d->kategori,
                    'jumlah' => $d->jumlah,
                    'harga_satuan' => $d->harga_satuan,
                    'subtotal' => $d->subtotal,
                ])->toArray(),
            ]);

            // Ambil resep berjalan untuk rawat jalan
            if ($bj->rekamMedis && $bj->rekamMedis->resep) {
                $resepRJ = $bj->rekamMedis->resep;
                if (in_array($resepRJ->status, ['Menunggu', 'Diproses', 'Selesai'])) {
                    $resepRJ->load(['details.obat', 'dokter']);
                    $resepBerjalan->push($resepRJ);
                }
            }
        }

        // Tambahkan resep rawat inap ke collection jika ada
        if ($rawatInapAktif) {
            $resepBerjalan = $resepBerjalan->concat($resepRawatInap);
        }

        // ===== 6. LAYANAN (dari database atau hardcoded) =====
        $layanan = [
            ['icon' => 'fa-stethoscope', 'warna' => 'blue', 'nama' => 'Poli Umum', 'desc' => 'Layanan pemeriksaan kesehatan umum dan konsultasi dokter umum.'],
            ['icon' => 'fa-tooth', 'warna' => 'green', 'nama' => 'Poli Gigi', 'desc' => 'Pemeriksaan dan perawatan kesehatan gigi serta mulut modern.'],
            ['icon' => 'fa-baby', 'warna' => 'pink', 'nama' => 'Poli KIA', 'desc' => 'Kesehatan Ibu & Anak, KB, Imunisasi, serta pemantauan tumbuh kembang anak.'],
            ['icon' => 'fa-truck-medical', 'warna' => 'red', 'nama' => 'UGD', 'desc' => 'Unit Gawat Darurat yang siap melayani penanganan medis kritis dan mendadak.'],
            ['icon' => 'fa-flask', 'warna' => 'purple', 'nama' => 'Laboratorium', 'desc' => 'Pengecekan sampel laboratorium medis yang steril, cepat, dan akurat.'],
            ['icon' => 'fa-spa', 'warna' => 'amber', 'nama' => 'Baby Spa', 'desc' => 'Layanan spa bayi khusus untuk menstimulasi tumbuh kembang anak dengan rileks.'],
        ];

        return view('dashboard_pasien', compact(
            'user',
            'pasien',
            'antrianAktif',
            'totalAntrianHariIni',
            'antrianSelesai',
            'antrianMenunggu',
            'antrianDilayani',
            'antrianPasienMenunggu',
            'pasienSelesaiHariIni',
            'riwayatAntrian',
            'riwayatDetail',
            'biayaBerlangsung',
            'resepBerjalan',
            'totalKunjungan',
            'layanan'
        ));
    }
}
