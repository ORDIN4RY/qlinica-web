<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamar';

    protected $fillable = [
        'kode_kamar',
        'nama_kamar',
        'kelas',
        'tarif_per_malam',
        'status',
        'kapasitas',
        'terisi',
    ];

    // Determine if the room is fully occupied
    public function isFull()
    {
        return $this->terisi >= $this->kapasitas;
    }

    // Get number of available beds
    public function availableBeds()
    {
        return max(0, $this->kapasitas - $this->terisi);
    }

    // Scope to fetch rooms that have at least one free bed
    public function scopeTersedia($query)
    {
        return $query->where('status', 'Tersedia')
                     ->whereRaw('terisi < kapasitas');
    }

}
