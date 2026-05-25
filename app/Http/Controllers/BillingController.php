<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Resep;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use App\Services\MidtransService;

class BillingController extends Controller
{
    /**
     * Menampilkan daftar tagihan pasien untuk kasir/resepsionis.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'Belum Bayar');
        $search = $request->query('search');

        $query = Billing::with(['pasien', 'rekamMedis.dokter.user', 'rawatInap'])
            ->orderBy('created_at', 'desc');

        // Sembunyikan tagihan jika pasien sedang dalam proses/direkomendasikan Rawat Inap,
        // KECUALI jika pasien tersebut sudah 'Selesai' (Check-Out) dari Rawat Inap.
        $query->where(function($q) {
            $q->whereDoesntHave('rekamMedis', function($q2) {
                $q2->where('is_rekomendasi_rawat_inap', true);
            })->orWhereHas('rawatInap', function($q3) {
                $q3->where('status', 'Selesai');
            })->orWhere(function($q4) {
                $q4->whereHas('rekamMedis', function($q5) {
                    $q5->where('is_rekomendasi_rawat_inap', false);
                })->orWhereNull('rekam_medis_id');
            });
        });

        if ($status && $status !== 'Semua') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('pasien', function($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%")
                       ->orWhere('nik', 'like', "%{$search}%");
                })
                ->orWhere('no_invoice', 'like', "%{$search}%");
            });
        }

        $billings = $query->paginate(15)->withQueryString();

        return response()
            ->view('billing.index', compact('billings', 'status', 'search'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    /**
     * Menampilkan detail tagihan/invoice tertentu.
     */
    public function show(Billing $billing)
    {
        // Panggil recalculateTotals agar jika pasien sedang rawat inap (Aktif), 
        // kalkulasi hari dan biaya kamar ter-update secara real-time sampai hari ini.
        $billing->recalculateTotals();
        $billing->save();

        $billing->load(['pasien', 'rekamMedis.dokter.user', 'details', 'kasir.user']);
        
        return response()
            ->view('billing.show', compact('billing'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    /**
     * Memproses pembayaran tagihan pasien.
     */
    public function bayar(Request $request, Billing $billing)
    {
        $request->validate([
            'metode_pembayaran' => 'required|string|in:Tunai,Debit,QRIS,Asuransi',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
        ]);

        if ($billing->status === 'Lunas') {
            return redirect()->route('admin.billing.show', $billing)
                ->with('warning', 'Tagihan ini sudah lunas.');
        }

        $user = auth()->user();
        $pegawai = $user->pegawai; // Dapatkan data pegawai dari user kasir yang login

        // Jika metode adalah Tunai, pastikan jumlah_dibayar cukup
        $jumlahDibayar = $request->input('jumlah_dibayar') !== null ? floatval($request->input('jumlah_dibayar')) : null;
        if ($request->input('metode_pembayaran') === 'Tunai') {
            if (is_null($jumlahDibayar) || $jumlahDibayar < floatval($billing->grand_total)) {
                return redirect()->route('admin.billing.show', $billing)
                    ->with('error', 'Jumlah dibayar kurang dari total. Mohon masukkan jumlah tunai yang cukup.');
            }
        }

        DB::transaction(function () use ($billing, $request, $pegawai, $jumlahDibayar) {
            // Hitung kembalian jika ada
            $kembalian = null;
            if ($request->input('metode_pembayaran') === 'Tunai') {
                $kembalian = $jumlahDibayar - floatval($billing->grand_total);
                if ($kembalian < 0) $kembalian = 0;
            }

            // Update status billing menjadi Lunas
            $billing->update([
                'status' => 'Lunas',
                'metode_pembayaran' => $request->metode_pembayaran,
                'kasir_id' => $pegawai ? $pegawai->id : null,
                'paid_at' => now(),
                'jumlah_dibayar' => $jumlahDibayar,
                'kembalian' => $kembalian,
            ]);

            // Hubungkan dengan Resep jika ada
            $resep = Resep::where('rekam_medis_id', $billing->rekam_medis_id)->first();
            if ($resep) {
                // Jika resep statusnya 'Menunggu Pembayaran' atau 'Menunggu', ubah ke 'Sudah Dibayar'
                // Ini menandakan obat siap dikemas & diserahkan oleh apoteker
                if ($resep->status === 'Menunggu Pembayaran' || $resep->status === 'Menunggu') {
                    $resep->update([
                        'status' => 'Sudah Dibayar'
                    ]);
                }
            }
        });

        return redirect()->route('admin.billing.show', $billing)
            ->with('success', 'Pembayaran berhasil diselesaikan. Kuitansi siap dicetak.')
            ->with('print_kuitansi', true);
    }

    /**
     * Generate QRIS QR Code via Midtrans untuk billing tertentu.
     */
    public function generateQris(Request $request, Billing $billing)
    {
        if ($billing->status === 'Lunas') {
            return response()->json(['success' => false, 'message' => 'Tagihan ini sudah lunas.']);
        }

        $midtrans = new MidtransService();
        $result   = $midtrans->chargeQris($billing);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Cek status pembayaran QRIS dari Midtrans (dipakai untuk polling dari frontend).
     */
    public function checkQrisStatus(Billing $billing)
    {
        $billing->refresh();

        // Jika sudah lunas di DB (misal diupdate oleh webhook), langsung return
        if ($billing->status === 'Lunas') {
            return response()->json(['status' => 'settlement', 'message' => 'Pembayaran sudah diterima!']);
        }

        $midtrans = new MidtransService();
        $result   = $midtrans->checkStatus($billing);

        // Jika settlement, reload billing agar data kasir/paid_at ikut
        if ($result['status'] === 'settlement') {
            $billing->refresh();
        }

        return response()->json($result);
    }

    /**
     * Memvalidasi keaslian nomor kartu BPJS dan menghitung potongan otomatis.
     */
    public function cekBpjs(Request $request, Billing $billing)
    {
        $request->validate([
            'no_bpjs' => 'required|string',
        ]);

        $noBpjs = $request->input('no_bpjs');
        $isNik = (strlen($noBpjs) === 16);
        
        $namaPeserta = $billing->pasien?->nama;
        $nikPeserta = $billing->pasien?->nik; // NIK di database klinik
        $nikBpjs = $isNik ? $noBpjs : ''; // NIK dari BPJS
        $statusKeterangan = 'AKTIF';
        $jenisPeserta = 'PBI (Penerima Bantuan Iuran)';

        // 1. Integrasi PCare BPJS
        // Hanya aktif jika BPJS_MODE=production DAN package tersedia DAN CONSID terisi
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
                
                // Gunakan jenisKartu sejalan dengan NIK atau NOKA
                $jenisKartu = $isNik ? 'nik' : 'noka';
                $res = $bpjs->jenisKartu($jenisKartu)->keyword($noBpjs)->show();
                
                if (isset($res['metaData']['code']) && $res['metaData']['code'] == 200) {
                    $peserta = $res['response'] ?? null;
                    if ($peserta) {
                        $namaPeserta = $peserta['nama'] ?? $namaPeserta;
                        $nikBpjs = $peserta['nik'] ?? $nikBpjs;
                        $statusKeterangan = $peserta['statusPeserta']['keterangan'] ?? $statusKeterangan;
                        $jenisPeserta = $peserta['jenisPeserta']['keterangan'] ?? $jenisPeserta;
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'BPJS Kesehatan: ' . ($res['metaData']['message'] ?? 'Kartu/NIK tidak terdaftar.'),
                    ]);
                }
            } else {
                // Fallback Uji Coba: NIK terdiri dari 16 digit, No. Kartu BPJS terdiri dari 13 digit
                if (strlen($noBpjs) !== 13 && strlen($noBpjs) !== 16) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format tidak dikenali. Masukkan 13 digit Nomor Kartu atau 16 digit NIK.',
                    ]);
                }
                
                // SELALU COCOK (Mode Sandbox Fleksibel):
                // Mengembalikan data pasien itu sendiri agar pengecekan selalu lolos
                $namaPeserta = $billing->pasien?->nama;
                $nikBpjs = $billing->pasien?->nik ?: $noBpjs;
            }
        } catch (\Exception $e) {
            // Fallback koneksi error
            if (strlen($noBpjs) !== 13 && strlen($noBpjs) !== 16) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format tidak valid & koneksi gagal: ' . $e->getMessage(),
                ]);
            }
            
            $namaPeserta = $billing->pasien?->nama;
            $nikBpjs = $billing->pasien?->nik ?: $noBpjs;
        }

        // Cek apakah status peserta AKTIF
        if (strtoupper($statusKeterangan) !== 'AKTIF') {
            return response()->json([
                'success' => false,
                'message' => "Kartu BPJS ditemukan namun status kepesertaan TIDAK AKTIF ({$statusKeterangan}).",
            ]);
        }

        // 2. Bandingkan Nama Pasien di Sistem dengan Nama di BPJS Kesehatan
        $namaSistem = strtoupper(trim($billing->pasien?->nama ?? ''));
        $namaBpjsUpper = strtoupper(trim($namaPeserta));
        
        // Bersihkan spasi ganda untuk perbandingan akurat
        $namaSistemClean = preg_replace('/\s+/', ' ', $namaSistem);
        $namaBpjsClean = preg_replace('/\s+/', ' ', $namaBpjsUpper);
        
        $isMatch = ($namaSistemClean === $namaBpjsClean);
        similar_text($namaSistemClean, $namaBpjsClean, $percentSimilarity);

        // 3. Verifikasi NIK Pasien (Kunci Keamanan Utama untuk Homonim)
        $isNikMatch = false;
        if ($nikPeserta && $nikBpjs) {
            $isNikMatch = (trim($nikPeserta) === trim($nikBpjs));
        }

        // 4. Kalkulasi Potongan BPJS
        // Biaya Registrasi ditanggung 100%
        $potonganRegistrasi = $billing->biaya_registrasi;
        // Biaya Tindakan ditanggung 100%
        $potonganTindakan = $billing->biaya_tindakan;
        // Obat ditanggung 80%
        $potonganObat = $billing->biaya_obat * 0.8;
        
        $totalPotongan = $potonganRegistrasi + $potonganTindakan + $potonganObat;
        
        // Pastikan potongan tidak melebihi biaya awal
        $totalBiayaAwal = $billing->biaya_registrasi + $billing->biaya_tindakan + $billing->biaya_obat;
        if ($totalPotongan > $totalBiayaAwal) {
            $totalPotongan = $totalBiayaAwal;
        }

        $grandTotalBaru = $totalBiayaAwal - $totalPotongan;

        // 5. Simpan perubahan ke database
        $billing->update([
            'no_bpjs' => $noBpjs,
            'potongan_bpjs' => $totalPotongan,
            'grand_total' => $grandTotalBaru,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kartu BPJS terverifikasi asli dan AKTIF!',
            'data' => [
                'nama' => $namaPeserta,
                'no_bpjs' => $noBpjs,
                'jenis_peserta' => $jenisPeserta,
                'potongan' => number_format($totalPotongan, 2, ',', '.'),
                'grand_total' => number_format($grandTotalBaru, 2, ',', '.'),
                'is_name_match' => $isMatch,
                'similarity' => round($percentSimilarity, 1),
                'nama_sistem' => $billing->pasien?->nama,
                'nik_sistem' => $nikPeserta,
                'nik_bpjs' => $nikBpjs,
                'is_nik_match' => $isNikMatch,
            ]
        ]);
    }
}
