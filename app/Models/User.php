<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'foto',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
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
     * Cek akses ke menu (dan opsional sub-akses tertentu).
     *
     * $menuName  = nama menu di tabel `menu` (misal 'Dashboard', 'Pegawai')
     * $subKey    = key sub-akses (misal 'view', 'tambah', 'admin_dashboard')
     *              Jika null → cukup cek apakah menu diaktifkan (ada row hak_akses dengan 'view' = true)
     *
     * Role 'admin' mendapat bypass penuh.
     * Role 'pasien' tidak punya akses pegawai apapun.
     */
    public function hasMenuAccess(string $menuName, ?string $subKey = null): bool
    {
        if ($this->role === 'pasien') return false;
        if ($this->role === 'admin')  return true;

        $pegawai = $this->pegawai;
        if (!$pegawai || !$pegawai->jabatan_id) return false;

        $ha = \App\Models\HakAkses::where('jabatan_id', $pegawai->jabatan_id)
            ->whereHas('menu', fn($q) => $q->where('nama_menu', $menuName))
            ->first();

        if (!$ha) return false;

        $sub = $ha->sub_akses ?? [];

        // Cek apakah menu aktif sama sekali (harus ada 'view' di sub_akses)
        if (!($sub['view'] ?? false)) return false;

        // Jika sub-key spesifik diminta, cek keberadaannya
        if ($subKey && $subKey !== 'lihat') {
            // Map legacy level names → sub_akses keys
            $key = match ($subKey) {
                'tambah' => 'tambah',
                'edit'   => 'edit',
                'hapus'  => 'hapus',
                default  => $subKey,
            };
            return !empty($sub[$key]);
        }

        return true;
    }

    /**
     * Dapatkan koleksi menu beserta sub-akses yang bisa diakses user.
     * Format: ['nama_menu' => ['view' => true, 'tambah' => false, ...]]
     */
    public function accessibleMenus(): \Illuminate\Support\Collection
    {
        if ($this->role === 'pasien') return collect();

        if ($this->role === 'admin') {
            $allMenuNames = ['Dashboard', 'Antrian Pemesanan', 'Antrian Pemeriksaan', 'Pasien', 'Pegawai', 'Resep', 'Obat',
                             'ICDX', 'Laporan', 'Komentar', 'Jabatan', 'Rekam Medis', 'Presensi', 'Billing', 'Kamar', 'Rawat Inap'];
            return collect($allMenuNames)->mapWithKeys(fn($menu) => [
                $menu => ['view' => true, 'tambah' => true, 'edit' => true, 'hapus' => true]
            ]);
        }

        $pegawai = $this->pegawai;
        if (!$pegawai || !$pegawai->jabatan_id) return collect();

        return \App\Models\HakAkses::with('menu')
            ->where('jabatan_id', $pegawai->jabatan_id)
            ->get()
            ->filter(fn($ha) => !empty(($ha->sub_akses ?? [])['view']))
            ->mapWithKeys(fn($ha) => [
                $ha->menu->nama_menu => $ha->sub_akses ?? []
            ]);
    }
}
