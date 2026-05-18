<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'pegawai_id',
        'jadwal_shift_id',
        'batch_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'telat_menit',
        'status',
        'approval_status',
        'keterangan',
        'surat_dokter',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function jadwalShift()
    {
        return $this->belongsTo(JadwalShift::class, 'jadwal_shift_id');
    }
}
