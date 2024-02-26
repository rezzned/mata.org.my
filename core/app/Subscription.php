<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'current_package_id',
        'status',
        'payment_status',
        'name',
        'email',
        'current_payment_method',
        'gateway_type'
    ];

    public function current_package()
    {
        return $this->belongsTo('App\Package', 'current_package_id');
    }

    public function next_package()
    {
        return $this->belongsTo('App\Package', 'next_package_id');
    }

    public function pending_package()
    {
        return $this->belongsTo('App\Package', 'pending_package_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
