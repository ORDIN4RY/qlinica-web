<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Resep;
use App\Models\Obat;

class ResepController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'Semua');
        $search = $request->query('search');

        $resepQuery = Resep::with(['rekamMedis.pasien', 'dokter.user', 'apoteker.user', 'details.obat'])
            ->orderByRaw("FIELD(status, 'Menunggu', 'Diproses', 'Selesai', 'Dibatalkan')")
            ->orderByDesc('created_at');

        if ($status && $status !== 'Semua') {
            $resepQuery->where('status', $status);
        }

        if ($search) {
            $resepQuery->where(function ($q) use ($search) {
                $q->whereHas('rekamMedis.pasien', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%{$search}%")
                ->orWhere('catatan_dokter', 'like', "%{$search}%");
            });
        }

        $resepList = $resepQuery->get();

        return view('apoteker.resep', [
            'resepList' => $resepList,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function update(Request $request, Resep $resep)
    {
        $request->validate([
            'action' => 'required|in:proses,selesai,kembalikan',
            'catatan_apoteker' => 'nullable|string|max:500',
        ]);

        $action = $request->input('action');
        $user = auth()->user();
        $apoteker = $user->pegawai;

        if (!$apoteker) {
            return redirect()->route('apoteker.resep')->with('error', 'Akun apoteker belum terdaftar sebagai pegawai.');
        }

        if ($action === 'proses' && $resep->status !== 'Menunggu') {
            return redirect()->route('apoteker.resep')->with('error', 'Resep hanya bisa diproses dari status Menunggu.');
        }

        if ($action === 'selesai' && !in_array($resep->status, ['Diproses', 'Sudah Dibayar', 'Menunggu Pembayaran'])) {
            return redirect()->route('apoteker.resep')->with('error', 'Resep hanya bisa diselesaikan jika sedang diproses atau sudah dibayar.');
        }

        if ($action === 'kembalikan' && $resep->status === 'Selesai') {
            return redirect()->route('apoteker.resep')->with('error', 'Resep yang sudah selesai tidak bisa dikembalikan.');
        }

        if ($action === 'selesai') {
            // Validasi apakah tagihan pasien sudah dibayar
            $billing = \App\Models\Billing::where('rekam_medis_id', $resep->rekam_medis_id)->first();
            if ($billing && $billing->status !== 'Lunas') {
                return redirect()->route('apoteker.resep')->with('error', 'Obat tidak dapat diserahkan karena tagihan billing pasien belum dibayar di Kasir.');
            }

            foreach ($resep->details as $detail) {
                $obat = Obat::find($detail->obat_id);

                if (!$obat) {
                    return redirect()->route('apoteker.resep')->with('error', "Obat yang diresepkan tidak ditemukan: {$detail->obat_id}");
                }

                if ($obat->stok < $detail->jumlah) {
                    return redirect()->route('apoteker.resep')->with('error', "Stok obat tidak cukup untuk {$obat->nama}. Stok saat ini: {$obat->stok}");
                }
            }
        }

        DB::transaction(function () use ($resep, $action, $apoteker, $request) {
            if ($action === 'proses') {
                $totalHargaObat = 0;

                // Cari atau buat billing untuk rekam medis ini
                $noInvoice = 'INV-' . now()->format('Ymd') . '-' . str_pad($resep->rekam_medis_id, 4, '0', STR_PAD_LEFT);
                $billing = \App\Models\Billing::firstOrCreate(
                    ['rekam_medis_id' => $resep->rekam_medis_id],
                    [
                        'pasien_id' => $resep->rekamMedis->pasien_id,
                        'no_invoice' => $noInvoice,
                        'biaya_registrasi' => 50000.00,
                        'biaya_tindakan' => $resep->rekamMedis->tindakan ? 75000.00 : 0.00,
                        'biaya_obat' => 0.00,
                        'grand_total' => 50000.00 + ($resep->rekamMedis->tindakan ? 75000.00 : 0.00),
                        'status' => 'Belum Bayar',
                    ]
                );

                // Hapus detail obat lama di billing_detail jika ada (untuk menghindari duplikasi jika re-proses)
                \App\Models\BillingDetail::where('billing_id', $billing->id)
                    ->where('kategori', 'Obat')
                    ->delete();

                foreach ($resep->details as $detail) {
                    $obat = $detail->obat;
                    if ($obat) {
                        $subtotal = $detail->jumlah * $obat->harga;
                        $totalHargaObat += $subtotal;

                        // Tambahkan detail obat ke billing_detail
                        \App\Models\BillingDetail::create([
                            'billing_id' => $billing->id,
                            'nama_item' => 'Obat: ' . $obat->nama . ' (' . ($detail->dosis ?: 'Sesuai Aturan') . ')',
                            'kategori' => 'Obat',
                            'jumlah' => $detail->jumlah,
                            'harga_satuan' => $obat->harga,
                            'subtotal' => $subtotal,
                        ]);
                    }
                }

                // Update total biaya obat & grand_total pada billing
                $billing->update([
                    'biaya_obat' => $totalHargaObat,
                    'grand_total' => $billing->biaya_registrasi + $billing->biaya_tindakan + $totalHargaObat,
                ]);

                $resep->update([
                    'status' => 'Menunggu Pembayaran',
                    'apoteker_id' => $apoteker->id,
                    'diproses_at' => now(),
                    'catatan_apoteker' => $request->input('catatan_apoteker'),
                ]);
            }

            if ($action === 'selesai') {
                foreach ($resep->details as $detail) {
                    $obat = Obat::find($detail->obat_id);
                    $obat->decrement('stok', $detail->jumlah);
                }

                $resep->update([
                    'status' => 'Selesai',
                    'apoteker_id' => $apoteker->id,
                    'selesai_at' => now(),
                    'catatan_apoteker' => $request->input('catatan_apoteker'),
                ]);
            }

            if ($action === 'kembalikan') {
                $resep->update([
                    'status' => 'Dibatalkan',
                    'apoteker_id' => $apoteker->id,
                    'catatan_apoteker' => $request->input('catatan_apoteker') ?: 'Resep dikembalikan oleh apoteker.',
                ]);
            }
        });

        $message = match ($action) {
            'proses' => 'Resep berhasil dikalkulasi harganya dan dikirim ke Kasir untuk pembayaran.',
            'selesai' => 'Resep selesai dan stok obat diperbarui.',
            'kembalikan' => 'Resep berhasil dikembalikan.',
        };

        return redirect()->route('apoteker.resep')->with('success', $message);
    }
}
