<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;

    public const NONE_MEMBER = 'none_member';
    public const ASSOCIATE_MEMBER = 'associate_member';
    public const STANDARD_MEMBER = 'standard_member';

    public static $members = [
        self::NONE_MEMBER => 'None Member',
        self::ASSOCIATE_MEMBER => 'Associate Member',
        self::STANDARD_MEMBER => 'Standard Member',
    ];

    protected $fillable = ['event_id', 'type', 'cost', 'available'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
