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

        if ($action === 'proses') {
            if ($resep->status !== 'Menunggu') {
                return redirect()->route('apoteker.resep')->with('error', 'Resep hanya bisa diproses dari status Menunggu.');
            }

            // Validasi stok sejak tahap skrining/proses agar pasien tidak membayar obat yang kosong
            foreach ($resep->details as $detail) {
                $obat = Obat::find($detail->obat_id);
                if (!$obat) {
                    return redirect()->route('apoteker.resep')->with('error', "Obat yang diresepkan tidak ditemukan: {$detail->obat_id}");
                }
                if ($obat->stok < $detail->jumlah) {
                    return redirect()->route('apoteker.resep')->with('error', "Stok obat tidak cukup untuk {$obat->nama}. Stok saat ini: {$obat->stok}, dibutuhkan: {$detail->jumlah}.");
                }
            }
        }

        if ($action === 'selesai') {
            if (!in_array($resep->status, ['Diproses', 'Sudah Dibayar', 'Menunggu Pembayaran'])) {
                return redirect()->route('apoteker.resep')->with('error', 'Resep hanya bisa diselesaikan jika sedang diproses atau sudah dibayar.');
            }

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

        if ($action === 'kembalikan') {
            if (!in_array($resep->status, ['Menunggu', 'Menunggu Pembayaran'])) {
                return redirect()->route('apoteker.resep')->with('error', 'Resep hanya bisa dikembalikan jika statusnya Menunggu atau Menunggu Pembayaran.');
            }
        }

        $oldStatus = $resep->status;

        try {
            DB::transaction(function () use ($resep, $action, $apoteker, $request, $oldStatus) {
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

                    // Update total biaya obat & hitung ulang total dengan BPJS dinamis
                    $billing->update([
                        'biaya_obat' => $totalHargaObat,
                    ]);
                    $billing->recalculateTotals();
                    $billing->save();

                    $resep->update([
                        'status' => 'Menunggu Pembayaran',
                        'apoteker_id' => $apoteker->id,
                        'diproses_at' => now(),
                        'catatan_apoteker' => $request->input('catatan_apoteker'),
                    ]);
                }

                if ($action === 'selesai') {
                    foreach ($resep->details as $detail) {
                        // Kunci stok obat saat update demi keamanan konkurensi (patient safety)
                        $obat = Obat::lockForUpdate()->findOrFail($detail->obat_id);
                        if ($obat->stok < $detail->jumlah) {
                            throw new \Exception("Stok obat {$obat->nama} mendadak tidak mencukupi saat penyerahan.");
                        }
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
                    // Jika resep berstatus Menunggu Pembayaran, hapus dari tagihan kasir (Batalkan & Tarik Tagihan)
                    if ($oldStatus === 'Menunggu Pembayaran') {
                        $billing = \App\Models\Billing::where('rekam_medis_id', $resep->rekam_medis_id)->first();
                        if ($billing && $billing->status === 'Belum Bayar') {
                            // Hapus rincian obat dari invoice
                            \App\Models\BillingDetail::where('billing_id', $billing->id)
                                ->where('kategori', 'Obat')
                                ->delete();

                            // Set biaya obat menjadi 0 dan hitung ulang total dengan BPJS
                            $billing->update([
                                'biaya_obat' => 0.00,
                            ]);
                            $billing->recalculateTotals();
                            $billing->save();
                        }
                    }

                    $resep->update([
                        'status' => 'Dibatalkan',
                        'apoteker_id' => $apoteker->id,
                        'catatan_apoteker' => $request->input('catatan_apoteker') ?: 'Resep dikembalikan oleh apoteker.',
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('apoteker.resep')->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }

        $message = match ($action) {
            'proses' => 'Resep berhasil dikalkulasi harganya dan dikirim ke Kasir untuk pembayaran.',
            'selesai' => 'Resep selesai dan stok obat diperbarui.',
            'kembalikan' => 'Resep berhasil dikembalikan dan ditarik dari billing kasir.',
        };

        return redirect()->route('apoteker.resep')->with('success', $message);
    }
}
