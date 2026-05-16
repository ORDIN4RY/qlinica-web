<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalShift extends Model
{
    use HasFactory;

    protected $fillable = ['pegawai_id', 'shift_id', 'tanggal'];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function presensi()
    {
        return $this->hasOne(Presensi::class, 'jadwal_shift_id');
    }
}
