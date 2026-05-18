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
        'sub_akses',
    ];

    protected function casts(): array
    {
        return [
            'sub_akses'    => 'array',
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

    /**
     * Cek apakah sub-akses tertentu aktif.
     * Contoh: $ha->hasSub('view'), $ha->hasSub('admin_dashboard'), $ha->hasSub('tambah')
     */
    public function hasSub(string $key): bool
    {
        $sub = $this->sub_akses ?? [];
        return !empty($sub[$key]);
    }
}
