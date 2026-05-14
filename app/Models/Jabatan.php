<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'nama_jabatan',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'jabatan_id');
    }

    public function hakAkses()
    {
        return $this->hasMany(HakAkses::class, 'jabatan_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'hak_akses', 'jabatan_id', 'menu_id');
    }
}
