<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = ['title', 'details', 'subtitle', 'name', 'type', 'information'];
    public $timestamps = false;

    public function convertAutoData()
    {
        return json_decode($this->information, true);
    }

    public function getAutoDataText()
    {
        $text = $this->convertAutoData();
        return end($text);
    }

    public function showKeyword()
    {
        $data = $this->keyword == null ? 'other' : $this->keyword;
        return $data;
    }

    public function showCheckoutLink()
    {
        $link = '';
        $data = $this->keyword == null ? 'other' : $this->keyword;
        if ($data == 'paypal') {
            $link = route('front.paypal.submit');
        } else if ($data == 'stripe') {
            $link = route('front.stripe.submit');
        } else if ($data == 'razerms') {
            $link = route('front.razerms.submit');
        } else if ($data == 'razorpay') {
            $link = route('front.razorpay.submit');
        }
        return $link;
    }

    public function showForm()
    {
        $show = '';
        $data = $this->keyword == null ? 'other' : $this->keyword;
        $values = ['paypal'];
        if (in_array($data, $values)) {
            $show = 'no';
        } else {
            $show = 'yes';
        }
        return $show;
    }
}
