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
        'kasir_id',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
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
        if ($this->rawatInap && $this->rawatInap->kamar) {
            $tglMasuk = \Carbon\Carbon::parse($this->rawatInap->tgl_masuk);
            $tglKeluar = $this->rawatInap->status === 'Selesai' && $this->rawatInap->tgl_keluar 
                ? \Carbon\Carbon::parse($this->rawatInap->tgl_keluar) 
                : now();
                
            $durasiHari = $tglMasuk->startOfDay()->diffInDays($tglKeluar->startOfDay());
            if ($durasiHari == 0) $durasiHari = 1; // Minimal 1 hari
            
            $this->biaya_kamar = $durasiHari * $this->rawatInap->kamar->tarif_per_malam;
            
            // Sync/Update rincian item BillingDetail secara otomatis
            if ($this->id) {
                $kamarDetail = \App\Models\BillingDetail::where('billing_id', $this->id)
                    ->where('kategori', 'Kamar')
                    ->first();
                    
                $deskripsi = 'Sewa Kamar Inap: ' . $this->rawatInap->kamar->kode_bed . ' (' . $durasiHari . ' malam)';
                
                if ($kamarDetail) {
                    $kamarDetail->update([
                        'nama_item' => $deskripsi,
                        'jumlah' => $durasiHari,
                        'subtotal' => $this->biaya_kamar,
                    ]);
                } else {
                    \App\Models\BillingDetail::create([
                        'billing_id' => $this->id,
                        'nama_item' => $deskripsi,
                        'kategori' => 'Kamar',
                        'jumlah' => $durasiHari,
                        'harga_satuan' => $this->rawatInap->kamar->tarif_per_malam,
                        'subtotal' => $this->biaya_kamar,
                    ]);
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
                // Logika Rawat Jalan
                $potonganRegistrasi = $bReg;
                $potonganTindakan = $bTin;
                $potonganObat = $bOba * 0.8; // Cover 80%

                $totalPotongan = $potonganRegistrasi + $potonganTindakan + $potonganObat;
                if ($totalPotongan > $totalBiayaAwal) {
                    $totalPotongan = $totalBiayaAwal;
                }

                $this->potongan_bpjs = $totalPotongan;
                $this->grand_total = $totalBiayaAwal - $totalPotongan;
            }
        } else {
            $this->potongan_bpjs = 0.00;
            $this->grand_total = $totalBiayaAwal;
        }
    }
}

