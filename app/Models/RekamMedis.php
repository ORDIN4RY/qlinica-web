<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekamMedis extends Model
{
    use SoftDeletes;

    protected $table = 'rekam_medis';

    protected $fillable = [
        'antrian_id',
        'pasien_id',
        'dokter_id',
        'tanggal_periksa',
        'anamnesis',
        'pemeriksaan_fisik',
        'tekanan_darah',
        'suhu',
        'berat_badan',
        'tinggi_badan',
        'nadi',
        'respirasi',
        'tindakan',
        'prognosa',
        'keadaan_keluar',
        'rujukan_ke',
        'catatan',
    ];

    protected $casts = [
        'tanggal_periksa' => 'datetime',
    ];

    public function antrian()
    {
        return $this->belongsTo(Antrian::class, 'antrian_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function dokter()
    {
        return $this->belongsTo(Pegawai::class, 'dokter_id');
    }

    public function diagnosa()
    {
        return $this->hasMany(RekamMedisDiagnosa::class, 'rekam_medis_id');
    }

    public function resep()
    {
        return $this->hasOne(\App\Models\Resep::class, 'rekam_medis_id');
    }

    public function diagnosaPrimer()
    {
        return $this->hasOne(RekamMedisDiagnosa::class, 'rekam_medis_id')->where('is_primer', true);
    }
}
