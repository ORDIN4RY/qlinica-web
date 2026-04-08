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

    public function jadwalDokter()
    {
        return $this->belongsTo(JadwalDokter::class, 'jadwal_dokter_id');
    }

    public function rekamMedis()
    {
        return $this->hasOne(RekamMedis::class, 'antrian_id');
    }
}
