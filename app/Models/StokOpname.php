<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $table = 'stok_opname';

    protected $fillable = [
        'obat_id',
        'pegawai_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
