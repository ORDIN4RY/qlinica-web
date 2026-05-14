<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'nama_menu',
    ];

    public function hakAkses()
    {
        return $this->hasMany(HakAkses::class, 'menu_id');
    }

    public function jabatans()
    {
        return $this->belongsToMany(Jabatan::class, 'hak_akses', 'menu_id', 'jabatan_id');
    }
}
