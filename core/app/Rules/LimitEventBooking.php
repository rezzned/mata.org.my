<?php

namespace App\Rules;

use App\EventDetail;
use Illuminate\Contracts\Validation\Rule;

class LimitEventBooking implements Rule
{
    public $event_id;

    public function __construct($event_id)
    {
        $this->event_id = $event_id;
    }

    public function passes($attribute, $value)
    {
        $ev_count = EventDetail::whereEventId($this->event_id)->whereIcNumber($value)->count();
        if ($ev_count > 0) {
            return false;
        }
        return true;
    }

    public function message()
    {
        return "You have already booked a ticket! You can't book more than one ticket";
    }
}
