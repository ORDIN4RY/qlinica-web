<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Antrian extends Model
{
    use SoftDeletes;

    protected $table = 'antrian';

    protected $fillable = [
        'no_antrian',
        'pasien_id',
        'jadwal_dokter_id',
        'tanggal',
        'jenis',
        'keluhan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function getNomorAntrianAttribute()
    {
        return $this->attributes['no_antrian'] ?? null;
    }

    public function getJenisPemesanAttribute()
    {
        return $this->attributes['jenis'] ?? null;
    }

    public function getStatusAttribute($value)
    {
        if ($value === 'Dipanggil') {
            return 'Terpanggil';
        }

        return $value;
    }
}
