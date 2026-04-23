<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icdx extends Model
{
    protected $table = 'icdx';

    protected $primaryKey = 'id_icdx';

    protected $fillable = [
        'kd_icdx',
        'nama_icdx',
    ];

    public function diagnosa()
    {
        return $this->hasMany(RekamMedisDiagnosa::class, 'icdx_id');
    }
}
