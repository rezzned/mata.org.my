<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventCertificate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function detail()
    {
        return $this->belongsTo(EventDetail::class);
    }
}
