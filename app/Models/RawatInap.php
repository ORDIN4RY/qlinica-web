<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawatInap extends Model
{
    protected $table = 'rawat_inap';

    protected $fillable = [
        'pasien_id',
        'kamar_id',
        'dokter_id',
        'jenis_penjamin',
        'no_sep',
        'tgl_masuk',
        'tgl_keluar',
        'status',
        'catatan_keluar',
    ];

    protected $casts = [
        'tgl_masuk' => 'datetime',
        'tgl_keluar' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function dokter()
    {
        return $this->belongsTo(Pegawai::class, 'dokter_id');
    }

    public function billing()
    {
        return $this->hasOne(Billing::class, 'rawat_inap_id');
    }

    public function kamarHistories()
    {
        return $this->hasMany(RawatInapKamarHistory::class, 'rawat_inap_id');
    }
}

