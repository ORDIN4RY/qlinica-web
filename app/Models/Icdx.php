<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icdx extends Model
{
    protected $table = 'icdx';

    protected $fillable = [
        'kode',
        'nama',
        'nama_en',
    ];

    public function diagnosa()
    {
        return $this->hasMany(RekamMedisDiagnosa::class, 'icdx_id');
    }
}
