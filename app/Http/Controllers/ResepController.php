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

        if ($action === 'selesai' && $resep->status !== 'Diproses') {
            return redirect()->route('apoteker.resep')->with('error', 'Resep hanya bisa diselesaikan jika sedang diproses.');
        }

        if ($action === 'kembalikan' && $resep->status === 'Selesai') {
            return redirect()->route('apoteker.resep')->with('error', 'Resep yang sudah selesai tidak bisa dikembalikan.');
        }

        if ($action === 'selesai') {
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
                $resep->update([
                    'status' => 'Diproses',
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
            'proses' => 'Resep berhasil diproses.',
            'selesai' => 'Resep selesai dan stok obat diperbarui.',
            'kembalikan' => 'Resep berhasil dikembalikan.',
        };

        return redirect()->route('apoteker.resep')->with('success', $message);
    }
}
