<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'model',
        'model_id',
        'order_id',
        'order_data',
        'item_model',
        'item_id',
        'amount',
        'trx_id',
        'payment_method',
        'payment_data',
        'status',
    ];

    protected $casts = [
        'order_data' => 'array',
        'payment_data' => 'array'
    ];
}
