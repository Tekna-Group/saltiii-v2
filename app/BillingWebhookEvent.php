<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingWebhookEvent extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_customer_id',
        'subscription_id',
        'event_name',
        'status',
        'webhook_url',
        'payload',
        'response_status',
        'response_body',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];
}
