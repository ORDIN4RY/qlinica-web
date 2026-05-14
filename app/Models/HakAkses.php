<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    use HasFactory;

    protected $table = 'hak_akses';

    protected $fillable = [
        'jabatan_id',
        'menu_id',
        'bisa_lihat',
        'bisa_tambah',
        'bisa_edit',
        'bisa_hapus',
    ];

    protected function casts(): array
    {
        return [
            'bisa_lihat' => 'boolean',
            'bisa_tambah' => 'boolean',
            'bisa_edit' => 'boolean',
            'bisa_hapus' => 'boolean',
        ];
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
