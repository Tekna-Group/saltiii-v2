<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeCustomer extends Model
{
    //
    protected $fillable = [
        'user_id',
        'stripe_customer_id',
        'stripe_payment_method_id',
        'subscription_id',
        'plan_id',
        'status',
        'next_billing_date',
    ];
}
