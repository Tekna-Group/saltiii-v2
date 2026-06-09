<?php

namespace App\Services;

use App\BillingWebhookEvent;
use App\StripeCustomer;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class GHLService
{
    protected $client;
    protected $locationId;
    protected $signupWebhookUrl;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.ghl.url', 'https://services.leadconnectorhq.com/'),
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.ghl.key'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Version'       => '2021-07-28',
            ],
        ]);

        $this->locationId = config('services.ghl.location_id');
        $this->signupWebhookUrl = config('services.ghl.signup_webhook_url');
    }

    public function createContact($data)
    {
        try {
            $response = $this->client->post('contacts/', [
                'json' => [
                    'firstName'  => $data['firstName'] ?? null,
                    'lastName'   => $data['lastName'] ?? null,
                    'email'      => $data['email'] ?? null,
                    'phone'      => $data['phone'] ?? null,
                    'locationId' => $this->locationId,
                    'tags'       => $data['tags'] ?? [], // ✅ Add tags here
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody()->getContents(), true);
                return [
                    'error'   => true,
                    'message' => $error,
                    'status'  => $e->getResponse()->getStatusCode(),
                ];
            }

            return [
                'error'   => true,
                'message' => $e->getMessage(),
                'status'  => 500,
            ];
        }
    }

    public function sendSignupWebhook(User $user, $signupMethod = 'email')
    {
        if (!$this->signupWebhookUrl) {
            return [
                'error' => true,
                'message' => 'GHL signup webhook URL is not configured.',
                'status' => 0,
            ];
        }

        try {
            $response = $this->client->post($this->signupWebhookUrl, [
                'json' => $this->buildSignupPayload($user, $signupMethod),
                'timeout' => 10,
            ]);

            $body = (string) $response->getBody();
            $decoded = $body ? json_decode($body, true) : null;

            return $decoded ?: [
                'success' => true,
                'status' => $response->getStatusCode(),
                'body' => $body,
            ];
        } catch (RequestException $e) {
            $message = $this->getExceptionMessage($e);

            \Log::warning('GHL Signup Webhook Error', [
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
                'message' => $message,
            ]);

            return [
                'error' => true,
                'message' => $message,
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
            ];
        }
    }

    public function sendBillingEvent($eventName, User $user, StripeCustomer $subscription = null, array $extra = [])
    {
        $webhookUrl = config("services.ghl.billing_webhooks.{$eventName}");

        if (!$webhookUrl) {
            return BillingWebhookEvent::create([
                'user_id' => $user->id,
                'stripe_customer_id' => $subscription ? $subscription->stripe_customer_id : null,
                'subscription_id' => $subscription ? $subscription->subscription_id : null,
                'event_name' => $eventName,
                'status' => 'skipped',
                'error_message' => 'GHL billing webhook URL is not configured.',
                'payload' => $this->buildBillingPayload($eventName, $user, $subscription, $extra),
            ]);
        }

        $payload = $this->buildBillingPayload($eventName, $user, $subscription, $extra);
        $event = BillingWebhookEvent::create([
            'user_id' => $user->id,
            'stripe_customer_id' => $subscription ? $subscription->stripe_customer_id : null,
            'subscription_id' => $subscription ? $subscription->subscription_id : null,
            'event_name' => $eventName,
            'status' => 'pending',
            'webhook_url' => $webhookUrl,
            'payload' => $payload,
        ]);

        try {
            $response = $this->client->post($webhookUrl, [
                'json' => $payload,
                'timeout' => 10,
            ]);

            $event->update([
                'status' => 'sent',
                'response_status' => $response->getStatusCode(),
                'response_body' => (string) $response->getBody(),
                'sent_at' => Carbon::now(),
            ]);
        } catch (RequestException $e) {
            $event->update([
                'status' => 'failed',
                'response_status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
                'error_message' => is_array($this->getExceptionMessage($e))
                    ? json_encode($this->getExceptionMessage($e))
                    : $this->getExceptionMessage($e),
            ]);

            \Log::warning('GHL Billing Webhook Error', [
                'event_name' => $eventName,
                'user_id' => $user->id,
                'status' => $event->response_status,
                'message' => $event->error_message,
            ]);
        }

        return $event;
    }

    public function hasSentBillingEvent(User $user, $eventName, $subscriptionId = null)
    {
        $query = BillingWebhookEvent::where('user_id', $user->id)
            ->where('event_name', $eventName)
            ->where('status', 'sent');

        if ($subscriptionId) {
            $query->where('subscription_id', $subscriptionId);
        }

        return $query->exists();
    }

    public function startFreeTrial(User $user, $days = null)
    {
        $days = $days ?: config('services.ghl.free_trial_days', 30);
        $trialEndsAt = Carbon::now()->addDays($days);

        $subscription = StripeCustomer::firstOrNew([
            'user_id' => $user->id,
        ]);

        if (
            $subscription->exists &&
            $subscription->status === 'active' &&
            $subscription->next_billing_date &&
            Carbon::parse($subscription->next_billing_date)->gte(Carbon::now())
        ) {
            return $subscription;
        }

        $subscription->fill([
            'plan_id' => config('services.ghl.free_trial_plan_id', 'free_trial_30_days'),
            'status' => 'active',
            'next_billing_date' => $trialEndsAt,
        ]);

        $subscription->save();

        return $subscription;
    }

    protected function buildSignupPayload(User $user, $signupMethod)
    {
        list($firstName, $lastName) = $this->splitName($user->name);
        $trialEndsAt = Carbon::now()->addDays(config('services.ghl.free_trial_days', 30));

        return [
            'email' => $user->email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'event_name' => config('services.ghl.signup_event_name', 'real_signup'),
            'trial_status' => config('services.ghl.signup_trial_status', 'active'),
            'signup_method' => $signupMethod,
            'trial_days' => config('services.ghl.free_trial_days', 30),
            'trial_starts_at' => Carbon::now()->toDateTimeString(),
            'trial_ends_at' => $trialEndsAt->toDateTimeString(),
        ];
    }

    protected function buildBillingPayload($eventName, User $user, StripeCustomer $subscription = null, array $extra = [])
    {
        list($firstName, $lastName) = $this->splitName($user->name);

        return array_merge([
            'email' => $user->email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'event_name' => $eventName,
            'status' => $subscription ? $subscription->status : null,
            'stripe_customer_id' => $subscription ? $subscription->stripe_customer_id : null,
            'subscription_id' => $subscription ? $subscription->subscription_id : null,
            'plan_id' => $subscription ? $subscription->plan_id : null,
            'next_billing_date' => $subscription && $subscription->next_billing_date
                ? Carbon::parse($subscription->next_billing_date)->toDateTimeString()
                : null,
            'sent_from' => config('app.name', 'Saltiii'),
            'sent_at' => Carbon::now()->toDateTimeString(),
        ], $extra);
    }

    protected function splitName($name)
    {
        $name = trim((string) $name);

        if ($name === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $name, 2);

        return [
            $parts[0],
            isset($parts[1]) ? $parts[1] : '',
        ];
    }

    protected function getExceptionMessage(RequestException $e)
    {
        if ($e->hasResponse()) {
            $body = (string) $e->getResponse()->getBody();
            $decoded = json_decode($body, true);

            return $decoded ?: $body;
        }

        return $e->getMessage();
    }
}
