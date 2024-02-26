<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    protected $table = "event_details";
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'amount',
        'quantity',
        'currency',
        'currency_symbol',
        'transaction_id',
        'status',
        'receipt',
        'transaction_details',
        'bex_details',
        'event_id',
        'payment_method',
        'cpd_points',
        'completed',
        'attendance',
        'event_ticket_id',
        'ic_number',
        'company_name',
        'professional_member',
        'address'
    ];
    protected $lazy = true;

    public function event()
    {
        return $this->belongsTo('App\Event', 'event_id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\EventTicket', 'event_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function certificate()
    {
        return $this->hasOne(EventCertificate::class, 'event_detail_id');
    }

    public function paymentGateway()
    {
        $gatewayType = Str::of($this->transaction_details)->replace(['\"', '"', "\'", "'"], '');
        if ($gatewayType == "offline") {
            return $this->belongsTo(OfflineGateway::class, 'payment_method');
        } else {
            return $this->belongsTo(PaymentGateway::class, 'payment_method');
        }
    }

    public function attendances()
    {
        return $this->hasOne('App\Attendance', 'event_detail_id', 'id');
    }
}
