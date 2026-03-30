<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nik',
        'age',
        'gender',
        'address',
        'phone',
        'disease',
        'visit_date',
        'notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];
}
