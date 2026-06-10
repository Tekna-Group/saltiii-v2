<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
    ],
    'ghl' => [
        'url' => env('GHL_API_URL', 'https://services.leadconnectorhq.com/'),
        'key' => env('GHL_API_KEY'),
        'location_id' => env('GHL_LOCATION_ID'),
        'signup_webhook_url' => env('GHL_SIGNUP_WEBHOOK_URL'),
        'billing_webhooks' => [
            'paid_subscriber' => env('GHL_WEBHOOK_PAID_SUBSCRIBER', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/6GJfKTn7n8M8ENlO0qag'),
            'subscription_cancelled' => env('GHL_WEBHOOK_SUBSCRIPTION_CANCELLED', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/vNXjpb1j3KsCiH4nMCCB'),
            'returned_within_3_days' => env('GHL_WEBHOOK_RETURNED_WITHIN_3_DAYS', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/6594000d-235a-42fd-9d7e-434e4df24543'),
            'trial_limit_reached' => env('GHL_WEBHOOK_TRIAL_LIMIT_REACHED', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/ea5e0ef4-1ddd-43a2-ba6a-800df7ac79ba'),
            'trial_ends_no_subscription' => env('GHL_WEBHOOK_TRIAL_ENDS_NO_SUBSCRIPTION', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/0ac3f82c-8348-4cbb-9697-b53592cf90a9'),
            'no_login_7_days_during_trial' => env('GHL_WEBHOOK_NO_LOGIN_7_DAYS_DURING_TRIAL', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/905ffd1f-a314-4a6c-bd01-323a4968346a'),
            'reactivated_after_cancellation' => env('GHL_WEBHOOK_REACTIVATED_AFTER_CANCELLATION', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/8449efbb-ae41-4415-8684-4592dd8d0fa9'),
            'renewed_past_first_billing_cycle' => env('GHL_WEBHOOK_RENEWED_PAST_FIRST_BILLING_CYCLE', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/bbcca379-0e02-4496-aae2-94356de5e48c'),
            'completes_onboarding' => env('GHL_WEBHOOK_COMPLETES_ONBOARDING', 'https://services.leadconnectorhq.com/hooks/gMZGLZKBh9UHdhSJMjT3/webhook-trigger/ce5b89cb-5075-4a11-953d-1b1c97e9146f'),
        ],
        'signup_event_name' => env('GHL_SIGNUP_EVENT_NAME', 'real_signup'),
        'signup_trial_status' => env('GHL_SIGNUP_TRIAL_STATUS', 'active'),
        'free_trial_days' => env('GHL_FREE_TRIAL_DAYS', 30),
        'free_trial_plan_id' => env('GHL_FREE_TRIAL_PLAN_ID', 'free_trial_30_days'),
    ],

];
