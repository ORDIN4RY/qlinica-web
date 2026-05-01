<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Obat;
use App\Models\Resep;

class ResepDetail extends Model
{
    protected $table = 'resep_detail';

    protected $fillable = [
        'resep_id',
        'obat_id',
        'jumlah',
        'dosis',
        'aturan_pakai',
        'keterangan',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'resep_id');
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}
