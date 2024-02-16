<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    // public const PASSWORD_REGEX = "/^.*(?=.{6,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\d\x])(?=.*[@$!%*#?&]).*$/";
    public const PASSWORD_REGEX = "/^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])/";
    public const IMAGE_PATH = 'assets/front/img/user/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'photo',
        'username',
        'password',
        'number',
        'address',
        'city',
        'state',
        'country',
        'billing_fname',
        'billing_lname',
        'billing_email',
        'billing_photo',
        'billing_number',
        'billing_city',
        'billing_state',
        'billing_address',
        'billing_country',
        'shpping_fname',
        'shpping_lname',
        'shpping_email',
        'shpping_photo',
        'shpping_number',
        'shpping_city',
        'shpping_state',
        'shpping_address',
        'shpping_country',
        'status',
        'verification_link',
        'email_verified',
        'date_of_birth',
        'age',
        'gender',
        'nation',
        'idcard_no',
        'personal_phone',
        'company_phone',
        'company_email',
        'company_fax',
        'company_city',
        'company_state',
        'company_country',
        'current_income',
        'company_address',
        'cpd_point',
        'license_id',
        'license_expire_date',
        'license_expire_notify',
        'license_expire_notify_date',
        'associate_member_start_date',
        'open_password',
        'is_password_expire',
        'password_expire_date',
        'last_logged_in'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'open_password' => 'encrypted'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        return $this->fname . " " . $this->lname;
    }


    public function conversations()
    {
        return $this->hasMany('App\Conversation');
    }

    public function orders()
    {
        return $this->hasMany('App\ProductOrder');
    }

    public function order_items()
    {
        return $this->hasMany('App\OrderItem');
    }

    public function courseOrder()
    {
        return $this->hasMany('App\CoursePurchase');
    }

    public function course_reviews()
    {
        return $this->hasMany('App\CourseReview');
    }

    public function donation_details()
    {
        return $this->hasMany('App\DonationDetail');
    }

    public function event_details()
    {
        return $this->hasMany('App\EventDetail');
    }

    public function package_orders()
    {
        return $this->hasMany('App\PackageOrder');
    }

    public function product_reviews()
    {
        return $this->hasMany('App\ProductReview');
    }

    public function tickets()
    {
        return $this->hasMany('App\Ticket');
    }

    public function subscription()
    {
        return $this->hasOne('App\Subscription')->with('current_package');
    }

    public function cpd_required()
    {
        return $this->hasMany(CpdRequired::class);
    }

    public function is_member(string $type = 'none')
    {
        $subs = $this->subscription;
        if (!$subs || $subs->status == 3) {
            return false;
        }
        return $subs->current_package->type == $type;
    }
}
