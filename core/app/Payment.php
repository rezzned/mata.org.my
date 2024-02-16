<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getItemAttribute()
    {
        return json_decode($this->item_info);
    }
    public function getOrderAttribute()
    {
        return json_decode($this->order_info);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
