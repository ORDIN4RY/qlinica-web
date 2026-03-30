<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasien extends Model
{
    use SoftDeletes;

    protected $table = 'pasien';

    protected $fillable = [
        'user_id',
        'no_rm',
        'nik',
        'nama',
        'nama_kk',
        'tgl_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'alamat',
        'desa',
        'kota',
        'agama_id',
        'pendidikan_id',
        'pekerjaan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
