<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $obats     = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kategoriList = Obat::select('kategori')->distinct()->whereNotNull('kategori')->orderBy('kategori')->pluck('kategori');

        return view('apoteker.obat', compact('obats', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'         => 'nullable|string|max:20|unique:obat,kode',
            'nama'         => 'required|string|max:100',
            'satuan'       => 'nullable|string|max:20',
            'kategori'     => 'nullable|string|max:50',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga_beli'   => 'required|numeric|min:0',
            'harga'        => 'required|numeric|min:0',
            'keterangan'   => 'nullable|string',
        ]);

        $obat = Obat::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil ditambahkan.',
            'obat'    => $obat,
        ]);
    }

    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'kode'         => 'nullable|string|max:20|unique:obat,kode,' . $id,
            'nama'         => 'required|string|max:100',
            'satuan'       => 'nullable|string|max:20',
            'kategori'     => 'nullable|string|max:50',
            'stok_minimum' => 'required|integer|min:0',
            'harga_beli'   => 'required|numeric|min:0',
            'harga'        => 'required|numeric|min:0',
            'keterangan'   => 'nullable|string',
        ]);

        // Stok tidak boleh diedit manual secara langsung
        $obat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data obat berhasil diperbarui (stok hanya dapat disesuaikan via Stok Opname).',
            'obat'    => $obat,
        ]);
    }

    /**
     * Melakukan stok opname untuk obat tertentu.
     * SOP: Perubahan stok dicatat dalam riwayat selisih berserta penanggung jawab.
     */
    public function stokOpname(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'required|string|max:500',
        ]);

        $pegawai = auth()->user()->pegawai;

        \DB::transaction(function() use ($obat, $validated, $pegawai) {
            $stokSistem = $obat->stok;
            $stokFisik = $validated['stok_fisik'];
            $selisih = $stokFisik - $stokSistem;

            // Simpan riwayat stok opname
            \App\Models\StokOpname::create([
                'obat_id' => $obat->id,
                'pegawai_id' => $pegawai ? $pegawai->id : null,
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $stokFisik,
                'selisih' => $selisih,
                'keterangan' => $validated['keterangan'],
            ]);

            // Sesuaikan stok obat dengan stok fisik yang baru
            $obat->update([
                'stok' => $stokFisik,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Stok opname berhasil disimpan dan stok obat telah disesuaikan.',
        ]);
    }

    /**
     * Melakukan restok (tambah stok) obat dari pembelian/supplier.
     * SOP: Perubahan stok dicatat dalam riwayat dengan tipe restok dan penanggung jawab.
     */
    public function restok(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'jumlah_masuk' => 'required|integer|min:1',
            'supplier'     => 'required|string|max:100',
            'harga_beli'   => 'nullable|numeric|min:0',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        $pegawai = auth()->user()->pegawai;

        \DB::transaction(function() use ($obat, $validated, $pegawai) {
            $stokSistem = $obat->stok;
            $jumlahMasuk = $validated['jumlah_masuk'];
            $stokFisik = $stokSistem + $jumlahMasuk;

            $hargaBeliTerbaru = $validated['harga_beli'] ?? null;
            $infoHarga = "";
            
            if ($hargaBeliTerbaru !== null && $hargaBeliTerbaru > 0) {
                $obat->harga_beli = $hargaBeliTerbaru;
                $infoHarga = " (Harga Beli Baru: Rp " . number_format($hargaBeliTerbaru, 0, ',', '.') . ")";
            }

            $keteranganFormat = "Penerimaan/Restok: +{$jumlahMasuk} dari {$validated['supplier']}" . $infoHarga . ($validated['keterangan'] ? " ({$validated['keterangan']})" : "");

            // Simpan riwayat transaksi restok
            \App\Models\StokOpname::create([
                'obat_id' => $obat->id,
                'pegawai_id' => $pegawai ? $pegawai->id : null,
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $stokFisik,
                'selisih' => $jumlahMasuk,
                'keterangan' => $keteranganFormat,
            ]);

            // Update stok obat dan harga beli terbaru
            $obat->update([
                'stok' => $stokFisik,
                'harga_beli' => $hargaBeliTerbaru !== null && $hargaBeliTerbaru > 0 ? $hargaBeliTerbaru : $obat->harga_beli,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Stok obat berhasil ditambahkan.',
        ]);
    }

    /**
     * Mendapatkan riwayat stok opname obat tertentu.
     */
    public function riwayatStokOpname($id)
    {
        $riwayat = \App\Models\StokOpname::with('pegawai')
            ->where('obat_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->created_at->format('d M Y H:i'),
                    'petugas' => $item->pegawai ? $item->pegawai->nama : 'Sistem/Admin',
                    'stok_sistem' => $item->stok_sistem,
                    'stok_fisik' => $item->stok_fisik,
                    'selisih' => ($item->selisih > 0 ? '+' : '') . $item->selisih,
                    'keterangan' => $item->keterangan,
                ];
            });

        return response()->json([
            'success' => true,
            'riwayat' => $riwayat,
        ]);
    }

    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil dihapus.',
        ]);
    }

    /**
     * Menyajikan Laporan Analisis Penjualan & Finansial Apotek.
     */
    public function laporan(Request $request)
    {
        $now = now();
        
        // 1. Filter Tanggal / Periode (jika ada input dari form)
        $tglAwal = $request->get('tgl_awal');
        $tglAkhir = $request->get('tgl_akhir');
        
        $salesQuery = \App\Models\BillingDetail::where('kategori', 'obat')
            ->whereHas('billing', function($q) use ($tglAwal, $tglAkhir) {
                $q->where('status', 'Lunas');
                if ($tglAwal && $tglAkhir) {
                    $q->whereBetween('created_at', [$tglAwal . ' 00:00:00', $tglAkhir . ' 23:59:59']);
                } else {
                    $q->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                }
            });

        $sales = $salesQuery->get();
        $totalPenjualan = $sales->sum('subtotal');
        
        // Hitung HPP dan Margin Laba
        $totalHpp = 0;
        foreach ($sales as $sale) {
            $obat = Obat::where('nama', $sale->nama_item)->first();
            if ($obat) {
                $totalHpp += $obat->harga_beli * $sale->jumlah;
            } else {
                $totalHpp += $sale->harga_satuan * 0.80 * $sale->jumlah;
            }
        }
        $totalMargin = $totalPenjualan - $totalHpp;
        
        // Total Resep Diproses
        $resepQuery = \App\Models\Resep::where('status', 'Selesai');
        if ($tglAwal && $tglAkhir) {
            $resepQuery->whereBetween('selesai_at', [$tglAwal . ' 00:00:00', $tglAkhir . ' 23:59:59']);
        } else {
            $resepQuery->whereMonth('selesai_at', $now->month)
                       ->whereYear('selesai_at', $now->year);
        }
        $resepCount = $resepQuery->count();

        // 10 Obat Terlaris
        $topSelling = \App\Models\BillingDetail::select('nama_item')
            ->selectRaw('SUM(jumlah) as total_terjual')
            ->selectRaw('SUM(subtotal) as total_pendapatan')
            ->where('kategori', 'obat')
            ->whereHas('billing', function($q) {
                $q->where('status', 'Lunas');
            })
            ->groupBy('nama_item')
            ->orderByRaw('SUM(jumlah) DESC')
            ->limit(10)
            ->get();

        // Status Stok Obat
        $totalObat = Obat::count();
        if ($totalObat > 0) {
            $habis = Obat::where('stok', 0)->count();
            $menipis = Obat::where('stok', '>', 0)->whereRaw('stok <= stok_minimum')->count();
            $tersedia = Obat::whereRaw('stok > stok_minimum')->count();
            
            $pctHabis = round(($habis / $totalObat) * 100);
            $pctMenipis = round(($menipis / $totalObat) * 100);
            $pctTersedia = 100 - $pctHabis - $pctMenipis;
        } else {
            $pctHabis = 0; $pctMenipis = 0; $pctTersedia = 100;
        }

        // Grafik Penjualan 7 Hari Terakhir
        $grafikHari = [];
        $grafikData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $grafikHari[] = $date->translatedFormat('l');
            
            $dailySales = \App\Models\BillingDetail::where('kategori', 'obat')
                ->whereHas('billing', function($q) use ($date) {
                    $q->where('status', 'Lunas')
                      ->whereDate('created_at', $date->toDateString());
                })->sum('subtotal');
            $grafikData[] = (float)$dailySales;
        }

        return view('apoteker.laporan', compact(
            'totalPenjualan',
            'totalHpp',
            'totalMargin',
            'resepCount',
            'topSelling',
            'pctHabis',
            'pctMenipis',
            'pctTersedia',
            'grafikHari',
            'grafikData',
            'tglAwal',
            'tglAkhir'
        ));
    }

    /**
     * Menyajikan Dashboard Apoteker secara dinamis dengan Peringatan Stok Kritis.
     */
    public function dashboard()
    {
        // 1. Total Obat
        $totalObat = Obat::count();

        // 2. Resep Pending (Menunggu)
        $resepPending = \App\Models\Resep::where('status', 'Menunggu')->count();

        // 3. Stok Menipis (di bawah atau sama dengan stok_minimum)
        $stokMenipis = Obat::whereRaw('stok <= stok_minimum')->count();

        // 4. Penjualan Hari Ini (resep yang ditandai selesai hari ini)
        $penjualanHariIni = \App\Models\Resep::where('status', 'Selesai')
            ->whereDate('selesai_at', now()->toDateString())
            ->count();

        // 5. Daftar Obat Kritis (untuk widget alert baru)
        $obatKritis = Obat::whereRaw('stok <= stok_minimum')
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        // 6. Aktivitas Terbaru (riwayat terbaru dari StokOpname)
        $aktivitasTerbaru = \App\Models\StokOpname::with('obat', 'pegawai')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($item) {
                $waktu = $item->created_at->diffForHumans();
                if ($item->selisih > 0) {
                    $icon = 'fas fa-plus-circle text-blue-500';
                    $tipe = 'Restok';
                    $badge = 'bg-blue-100 text-blue-700';
                    $pesan = "Penerimaan stok obat '{$item->obat->nama}' sebanyak +{$item->selisih} unit dari supplier oleh " . ($item->pegawai ? $item->pegawai->nama : 'Petugas');
                } else {
                    $icon = 'fas fa-sync text-yellow-500';
                    $tipe = 'Stok Opname';
                    $badge = 'bg-yellow-100 text-yellow-700';
                    $pesan = "Stok opname obat '{$item->obat->nama}' disesuaikan menjadi {$item->stok_fisik} unit (selisih {$item->selisih}) oleh " . ($item->pegawai ? $item->pegawai->nama : 'Petugas');
                }
                return [
                    'icon' => $icon,
                    'pesan' => $pesan,
                    'waktu' => $waktu,
                    'badge' => $badge,
                    'tipe' => $tipe
                ];
            });

        return view('apoteker.dashboard', compact(
            'totalObat',
            'resepPending',
            'stokMenipis',
            'penjualanHariIni',
            'obatKritis',
            'aktivitasTerbaru'
        ));
    }
}
