<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpdRequired extends Model
{
    use HasFactory;
    protected $table = 'cpd_required';

    protected $fillable = [
        'user_id',
        'required_points',
        'year'
    ];
}
