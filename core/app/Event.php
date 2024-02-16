<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title',
        'slug',
        'short_desc',
        'content',
        'date',
        'time',
        // 'cost',
        // 'available_tickets',
        'organizer',
        'organizer_email',
        'organizer_phone',
        'organizer_website',
        'venue',
        'venue_location',
        'venue_phone',
        'meta_tags',
        'meta_description',
        'image',
        'video',
        'lang_id',
        'cat_id',
        'cpd_points',
        'short_form',
        'status',
        'datetime2',
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVE = 2;

    public function scopeActive()
    {
        return $this->where('status', self::STATUS_ACTIVE);
    }

    public function eventCategories()
    {
        return $this->belongsTo(EventCategory::class, 'cat_id', 'id');
    }

    public function eventTicket()
    {
        return $this->hasMany(EventTicket::class, 'event_id');
    }
}
