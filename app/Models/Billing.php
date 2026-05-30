<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billing';

    protected $fillable = [
        'rekam_medis_id',
        'rawat_inap_id',
        'pasien_id',
        'no_invoice',
        'no_bpjs',
        'biaya_registrasi',
        'biaya_kamar',
        'biaya_tindakan',
        'biaya_obat',
        'potongan_bpjs',
        'grand_total',
        'status',
        'metode_pembayaran',
        'jumlah_dibayar',
        'kembalian',
        'kasir_id',
        'paid_at',
        // Midtrans QRIS
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_qr_url',
        'midtrans_status',
        'midtrans_qr_generated_at',
    ];

    protected $casts = [
        'paid_at'                  => 'datetime',
        'midtrans_qr_generated_at' => 'datetime',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function kasir()
    {
        return $this->belongsTo(Pegawai::class, 'kasir_id');
    }

    public function details()
    {
        return $this->hasMany(BillingDetail::class, 'billing_id');
    }

    public function rawatInap()
    {
        return $this->belongsTo(RawatInap::class, 'rawat_inap_id');
    }

    /**
     * Menghitung ulang potongan BPJS (jika aktif) dan grand total billing.
     * SOP BPJS Rawat Inap & Jalan:
     * Jika rawat_inap aktif dan jenis penjamin BPJS, total ditanggung BPJS (diset menjadi klaim, grand_total pasien = 0).
     */
    public function recalculateTotals()
    {
        // 0. Update kalkulasi dinamis untuk Kamar jika terikat Rawat Inap
        if ($this->rawatInap) {
            // Dapatkan semua riwayat kamar
            $histories = $this->rawatInap->kamarHistories()->with('kamar')->orderBy('tgl_mulai', 'asc')->get();
            
            // Jika kosong (fallback), kita buat default
            if ($histories->isEmpty()) {
                $tglMasuk = \Carbon\Carbon::parse($this->rawatInap->tgl_masuk);
                $tglKeluar = $this->rawatInap->status === 'Selesai' && $this->rawatInap->tgl_keluar 
                    ? \Carbon\Carbon::parse($this->rawatInap->tgl_keluar) 
                    : now();
                $durasiHari = $tglMasuk->startOfDay()->diffInDays($tglKeluar->startOfDay());
                if ($durasiHari == 0) $durasiHari = 1;
                
                $this->biaya_kamar = $durasiHari * ($this->rawatInap->kamar->tarif_per_malam ?? 0);
                
                if ($this->id) {
                    \App\Models\BillingDetail::where('billing_id', $this->id)
                        ->where('kategori', 'Kamar')
                        ->delete();
                    
                    $deskripsi = 'Sewa Kamar Inap: ' . ($this->rawatInap->kamar->nama_kamar ?? '-') . ' (' . $durasiHari . ' malam)';
                    \App\Models\BillingDetail::create([
                        'billing_id' => $this->id,
                        'nama_item' => $deskripsi,
                        'kategori' => 'Kamar',
                        'jumlah' => $durasiHari,
                        'harga_satuan' => $this->rawatInap->kamar->tarif_per_malam ?? 0,
                        'subtotal' => $this->biaya_kamar,
                    ]);
                }
            } else {
                $totalBiayaKamar = 0;
                $detailsToInsert = [];
                $totalNights = 0;
                $activeHistory = null;
                
                foreach ($histories as $history) {
                    $start = \Carbon\Carbon::parse($history->tgl_mulai);
                    if (is_null($history->tgl_selesai)) {
                        $end = $this->rawatInap->status === 'Selesai' && $this->rawatInap->tgl_keluar 
                            ? \Carbon\Carbon::parse($this->rawatInap->tgl_keluar) 
                            : now();
                        $activeHistory = $history;
                    } else {
                        $end = \Carbon\Carbon::parse($history->tgl_selesai);
                    }
                    
                    $nights = $start->startOfDay()->diffInDays($end->startOfDay());
                    
                    // If a segment is closed (tgl_selesai is not null) but nights is 0,
                    // we default it to 1 night to ensure it gets charged/included in the bill.
                    if ($nights == 0 && !is_null($history->tgl_selesai)) {
                        $nights = 1;
                    }
                    
                    if ($nights > 0) {
                        $subtotal = $nights * $history->tarif_per_malam;
                        $totalBiayaKamar += $subtotal;
                        $totalNights += $nights;
                        
                        $detailsToInsert[] = [
                            'nama_item' => 'Sewa Kamar Inap: ' . ($history->kamar->nama_kamar ?? '-') . ' (' . ($history->kamar->kelas ?? '-') . ') (' . $nights . ' malam)',
                            'kategori' => 'Kamar',
                            'jumlah' => $nights,
                            'harga_satuan' => $history->tarif_per_malam,
                            'subtotal' => $subtotal,
                        ];
                    }
                }
                
                // Jika total_nights == 0, minimal di-charge 1 malam untuk kamar aktif/terakhir
                if ($totalNights == 0) {
                    $targetHistory = $activeHistory ?? $histories->last();
                    if ($targetHistory) {
                        $nights = 1;
                        $subtotal = $nights * $targetHistory->tarif_per_malam;
                        $totalBiayaKamar += $subtotal;
                        
                        $detailsToInsert[] = [
                            'nama_item' => 'Sewa Kamar Inap: ' . ($targetHistory->kamar->nama_kamar ?? '-') . ' (' . ($targetHistory->kamar->kelas ?? '-') . ') (' . $nights . ' malam)',
                            'kategori' => 'Kamar',
                            'jumlah' => $nights,
                            'harga_satuan' => $targetHistory->tarif_per_malam,
                            'subtotal' => $subtotal,
                        ];
                    }
                }
                
                $this->biaya_kamar = $totalBiayaKamar;
                
                // Sync/Update rincian item BillingDetail secara otomatis
                if ($this->id) {
                    // Hapus data Kamar lama
                    \App\Models\BillingDetail::where('billing_id', $this->id)
                        ->where('kategori', 'Kamar')
                        ->delete();
                    
                    // Masukkan data Kamar baru
                    foreach ($detailsToInsert as $detail) {
                        \App\Models\BillingDetail::create([
                            'billing_id' => $this->id,
                            'nama_item' => $detail['nama_item'],
                            'kategori' => 'Kamar',
                            'jumlah' => $detail['jumlah'],
                            'harga_satuan' => $detail['harga_satuan'],
                            'subtotal' => $detail['subtotal'],
                        ]);
                    }
                }
            }
        }

        // 1. Ambil nilai default 0 jika null
        $bReg = $this->biaya_registrasi ?? 0;
        $bKam = $this->biaya_kamar ?? 0;
        $bTin = $this->biaya_tindakan ?? 0;
        $bOba = $this->biaya_obat ?? 0;

        $totalBiayaAwal = $bReg + $bKam + $bTin + $bOba;

        if ($this->no_bpjs) {
            // Cek apakah ini transaksi rawat inap dengan penjamin BPJS
            if ($this->rawatInap && $this->rawatInap->jenis_penjamin === 'BPJS KESEHATAN') {
                // Seluruh biaya ditanggung BPJS (Masuk tagihan paket INA-CBG)
                // (Pada implementasi nyata akan ada selisih kelas jika naik kelas)
                $this->potongan_bpjs = $totalBiayaAwal;
                $this->grand_total = 0;
            } else {
                // Logika Rawat Jalan BPJS FKTP - semua gratis kecuali obat non-fornas
                // Registrasi, Tindakan = Rp 0 (ditanggung kapitasi)
                // Obat Fornas = Rp 0 (ditanggung BPJS)
                // Obat Non-Fornas = tetap ditagih (co-payment)
                $biayaNonFornas = $this->hitungBiayaObatNonFornas();
                
                // Pastikan grand total minimum adalah biaya non-fornas
                $potongan = $totalBiayaAwal - $biayaNonFornas;
                
                // Cegah potongan negatif jika ada kesalahan data
                if ($potongan < 0) {
                    $potongan = 0;
                }

                $this->potongan_bpjs = $potongan;
                $this->grand_total = $biayaNonFornas;
            }
        } else {
            $this->potongan_bpjs = 0.00;
            $this->grand_total = $totalBiayaAwal;
        }
    }

    /**
     * Menghitung total biaya obat yang tidak termasuk dalam Formularium Nasional
     * (Obat-obat ini ditanggung sendiri oleh pasien sebagai co-payment)
     * 
     * @return float
     */
    public function hitungBiayaObatNonFornas()
    {
        $totalNonFornas = 0;
        
        // Cek semua resep detail yang terhubung dengan billing ini melalui rekam medis
        if ($this->rekamMedis && $this->rekamMedis->resep) {
            foreach ($this->rekamMedis->resep->details as $detail) {
                if ($detail->obat && !$detail->obat->is_fornas) {
                    // Hitung subtotal untuk obat non-fornas
                    // Default harga adalah harga dari master obat jika tidak ada field harga_satuan di resep_detail
                    $harga = $detail->obat->harga ?? 0;
                    $totalNonFornas += ($detail->jumlah * $harga);
                }
            }
        }
        
        return $totalNonFornas;
    }
}

