<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Jabatan;
use App\Models\Pasien;
use App\Models\Pegawai;

class WelcomeController extends Controller
{
    /**
     * Tampilkan landing page publik dengan data dinamis dari database.
     */
    public function index()
    {
        // ===== 1. JUMLAH DOKTER =====
        $jumlahDokter = Pegawai::whereNotNull('spesialisasi')
            ->whereNull('deleted_at')
            ->count();

        // Fallback jika spesialisasi kosong, cari dari jabatan "Dokter"
        if ($jumlahDokter === 0) {
            $jumlahDokter = Pegawai::whereHas('jabatan', function ($q) {
                $q->where('nama_jabatan', 'like', '%Dokter%');
            })
                ->whereNull('deleted_at')
                ->count();
        }

        // Minimal 1 dokter untuk tampilan
        $jumlahDokter = max($jumlahDokter, 1);

        // ===== 2. JUMLAH PASIEN TERDAFTAR =====
        $jumlahPasien = Pasien::whereNull('deleted_at')->count();

        // ===== 3. JUMLAH TENAGA MEDIS / PEGAWAI =====
        $jumlahPegawai = Pegawai::whereNull('deleted_at')->count();

        // ===== 4. RATING RATA-RATA =====
        // Hitung dari tabel feedback (asumsi ada kolom rating)
        $ratingRatata = Feedback::whereNotNull('penilaian')
            ->avg('penilaian');
        
        $ratingRatata = $ratingRatata ? number_format($ratingRatata, 1) : 4.9; // Default 4.9 jika tidak ada data

        // ===== 5. TESTIMONIAL / FEEDBACK =====
        // Ambil 10 feedback terbaru — ulasan bersifat opsional,
        // yang tidak ada teks tetap ditampilkan (hanya bintang)
        $testimoni = Feedback::with('pasien')
            ->whereNotNull('penilaian')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($feedback) {
                $nama = $feedback->pasien?->nama ?? 'Pasien Qlinica';
                $words = array_filter(explode(' ', $nama));
                $inisial = strtoupper(implode('', array_map(fn($w) => $w[0], $words)));

                return [
                    'ulasan'   => $feedback->ulasan ?: null,   // null jika tidak ada teks
                    'penilaian' => (int) $feedback->penilaian,
                    'nama'     => $nama,
                    'inisial'  => $inisial,
                    'tipe'     => $feedback->penilaian >= 4 ? 'Pasien' : 'Pasien',
                ];
            });

        // ===== 6. LAYANAN (STATIC) =====
        $layanan = [
            ['icon' => 'fa-stethoscope', 'warna' => 'blue', 'nama' => 'Poli Umum', 'desc' => 'Pemeriksaan kesehatan umum dan konsultasi medis privat oleh dokter berpengalaman.'],
            ['icon' => 'fa-tooth', 'warna' => 'green', 'nama' => 'Poli Gigi', 'desc' => 'Perawatan kesehatan gigi, mulut, dan ortodonti dengan teknologi medis terbaru.'],
            ['icon' => 'fa-baby', 'warna' => 'pink', 'nama' => 'Poli KIA', 'desc' => 'Kesehatan Ibu dan Anak, KB, vaksinasi balita, serta konsultasi kehamilan menyeluruh.'],
            ['icon' => 'fa-truck-medical', 'warna' => 'red', 'nama' => 'UGD', 'desc' => 'Unit Gawat Darurat yang bersiaga penuh untuk memberikan tindakan pertolongan pertama.'],
            ['icon' => 'fa-flask', 'warna' => 'purple', 'nama' => 'Laboratorium', 'desc' => 'Pemeriksaan darah, urine, dan tes laboratorium cepat, steril, serta akurat.'],
            ['icon' => 'fa-spa', 'warna' => 'amber', 'nama' => 'Baby Spa', 'desc' => 'Pijat, spa, dan terapi stimulasi tumbuh kembang bayi yang menenangkan dan aman.'],
        ];

        return view('welcome', compact(
            'jumlahDokter',
            'jumlahPasien',
            'jumlahPegawai',
            'ratingRatata',
            'testimoni',
            'layanan'
        ));
    }
}
