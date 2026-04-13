<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    protected $table = 'jadwal_dokter';

    protected $fillable = [
        'pegawai_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota',
        'is_aktif',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function antrian()
    {
        return $this->hasMany(Antrian::class, 'jadwal_dokter_id');
    }
}
