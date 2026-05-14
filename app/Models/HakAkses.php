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
        'bisa_lihat',   // legacy — tetap dipertahankan untuk backward compat
        'bisa_tambah',  // legacy
        'bisa_edit',    // legacy
        'bisa_hapus',   // legacy
        'sub_akses',    // JSON — format baru: {"view": true, "admin_dashboard": true, ...}
    ];

    protected function casts(): array
    {
        return [
            'bisa_lihat'   => 'boolean',
            'bisa_tambah'  => 'boolean',
            'bisa_edit'    => 'boolean',
            'bisa_hapus'   => 'boolean',
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
