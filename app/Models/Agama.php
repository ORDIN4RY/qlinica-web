<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $table = 'agama';

    protected $fillable = ['agama'];

    public function pasiens()
    {
        return $this->hasMany(Pasien::class, 'agama_id');
    }
}
