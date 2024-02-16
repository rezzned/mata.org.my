<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlideBanner extends Model
{

    protected $fillable = ['lang_id','title','image', 'status'];

    

}
