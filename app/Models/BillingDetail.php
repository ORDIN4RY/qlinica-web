<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingDetail extends Model
{
    protected $table = 'billing_detail';

    protected $fillable = [
        'billing_id',
        'nama_item',
        'kategori',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billing_id');
    }
}
