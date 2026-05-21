<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Pegawai;
use App\Models\RekamMedis;
use App\Models\ResepDetail;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'rekam_medis_id',
        'rawat_inap_id',
        'dokter_id',
        'apoteker_id',
        'status',
        'catatan_dokter',
        'catatan_apoteker',
        'diproses_at',
        'selesai_at',
    ];

    protected $casts = [
        'diproses_at' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }

    public function rawatInap()
    {
        return $this->belongsTo(RawatInap::class, 'rawat_inap_id');
    }

    public function dokter()
    {
        return $this->belongsTo(Pegawai::class, 'dokter_id');
    }

    public function apoteker()
    {
        return $this->belongsTo(Pegawai::class, 'apoteker_id');
    }

    public function details()
    {
        return $this->hasMany(ResepDetail::class, 'resep_id');
    }
}
