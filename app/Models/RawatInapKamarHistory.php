<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawatInapKamarHistory extends Model
{
    protected $table = 'rawat_inap_kamar_history';

    protected $fillable = [
        'rawat_inap_id',
        'kamar_id',
        'tarif_per_malam',
        'tgl_mulai',
        'tgl_selesai',
    ];

    protected $casts = [
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
    ];

    public function rawatInap()
    {
        return $this->belongsTo(RawatInap::class, 'rawat_inap_id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
