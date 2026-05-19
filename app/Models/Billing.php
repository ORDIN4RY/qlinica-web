<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billing';

    protected $fillable = [
        'rekam_medis_id',
        'pasien_id',
        'no_invoice',
        'no_bpjs',
        'biaya_registrasi',
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
}
