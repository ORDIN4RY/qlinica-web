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
            'metode_pembayaran' => 'required|string|in:Tunai,Asuransi',
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
}
