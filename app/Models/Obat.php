<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ResepDetail;

class Obat extends Model
{
    use SoftDeletes;

    protected $table = 'obat';

    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'kategori',
        'stok',
        'stok_minimum',
        'harga_beli',
        'harga',
        'keterangan',
    ];

    public function resepDetails()
    {
        return $this->hasMany(ResepDetail::class, 'obat_id');
    }

    public function stokOpnames()
    {
        return $this->hasMany(StokOpname::class, 'obat_id');
    }
}
