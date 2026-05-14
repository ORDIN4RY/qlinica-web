<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }

    public function pegawai()
    {
        return $this->hasOne(\App\Models\Pegawai::class);
    }

    /**
     * Cek apakah user (pegawai) punya akses ke menu tertentu berdasarkan jabatannya.
     * Level: lihat | tambah | edit | hapus
     * Pasien selalu dianggap punya akses ke portal pasien saja (diatur route).
     */
    public function hasMenuAccess(string $menuName, ?string $level = null): bool
    {
        if ($this->role === 'pasien') {
            return false;
        }

        // Admin mendapat akses penuh ke semua menu
        if ($this->role === 'admin') {
            return true;
        }

        $pegawai = $this->pegawai;
        if (!$pegawai || !$pegawai->jabatan_id) {
            return false;
        }

        $query = \App\Models\HakAkses::where('jabatan_id', $pegawai->jabatan_id)
            ->whereHas('menu', fn($q) => $q->where('nama_menu', $menuName));

        if ($level) {
            $column = match ($level) {
                'lihat' => 'bisa_lihat',
                'tambah' => 'bisa_tambah',
                'edit' => 'bisa_edit',
                'hapus' => 'bisa_hapus',
                default => null,
            };
            if ($column) {
                $query->where($column, true);
            }
        }

        return $query->exists();
    }

    /**
     * Dapatkan koleksi menu beserta level akses yang bisa diakses user ini.
     * Format: ['nama_menu' => ['lihat'=>true, 'tambah'=>false, ...]]
     */
    public function accessibleMenus(): \Illuminate\Support\Collection
    {
        if ($this->role === 'pasien') {
            return collect();
        }

        // Admin mendapat akses ke semua menu yang ada
        if ($this->role === 'admin') {
            $allMenuNames = ['Dashboard', 'Antrian', 'Pasien', 'Pegawai', 'Resep', 'Obat',
                             'ICDX', 'Laporan', 'Komentar', 'Jabatan', 'Rekam Medis'];
            return collect($allMenuNames)->mapWithKeys(fn($menu) => [
                $menu => ['lihat' => true, 'tambah' => true, 'edit' => true, 'hapus' => true]
            ]);
        }

        $pegawai = $this->pegawai;
        if (!$pegawai || !$pegawai->jabatan_id) {
            return collect();
        }

        return \App\Models\HakAkses::with('menu')
            ->where('jabatan_id', $pegawai->jabatan_id)
            ->where('bisa_lihat', true)
            ->get()
            ->mapWithKeys(fn($ha) => [
                $ha->menu->nama_menu => [
                    'lihat' => $ha->bisa_lihat,
                    'tambah' => $ha->bisa_tambah,
                    'edit' => $ha->bisa_edit,
                    'hapus' => $ha->bisa_hapus,
                ]
            ]);
    }
}
