<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    /** Relasi ke tabel users */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi ke tabel agama */
    public function agama()
    {
        return $this->belongsTo(\App\Models\Agama::class, 'agama_id');
    }

    /** Relasi ke tabel pendidikan */
    public function pendidikan()
    {
        return $this->belongsTo(\App\Models\Pendidikan::class, 'pendidikan_id');
    }

    /** Relasi ke tabel pekerjaan */
    public function pekerjaan()
    {
        return $this->belongsTo(\App\Models\Pekerjaan::class, 'pekerjaan_id');
    }

    /** Accessor: hitung umur otomatis dari tgl_lahir */
    public function getUmurAttribute(): ?int
    {
        if (!$this->tgl_lahir) return null;
        return Carbon::parse($this->tgl_lahir)->age;
    }
}
