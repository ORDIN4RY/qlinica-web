<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekamMedisDiagnosa extends Model
{
    protected $table = 'rekam_medis_diagnosa';

    protected $fillable = [
        'rekam_medis_id',
        'icdx_id',
        'is_primer',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }

    public function icdx()
    {
        return $this->belongsTo(Icdx::class, 'icdx_id');
    }
}
