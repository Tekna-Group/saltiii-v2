<?php

namespace App\Http\Controllers;

use App\BillingWebhookEvent;
use App\Services\GHLService;
use App\StripeCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebhookTestController extends Controller
{
    protected $events = [
        'paid_subscriber' => 'Paid Subscriber',
        'subscription_cancelled' => 'Cancelled Subscription',
        'returned_within_3_days' => 'Returns Within 3 Days',
        'trial_limit_reached' => 'Trial Limit Reached / End Date',
        'reactivated_after_cancellation' => 'Reactivated After Cancellation',
        'renewed_past_first_billing_cycle' => 'Renewed Past First Billing Cycle',
        'completes_onboarding' => 'Completes Onboarding',
    ];

    public function index()
    {
        return view('webhook-buttons', [
            'events' => $this->events,
            'webhooks' => config('services.ghl.billing_webhooks', []),
            'logs' => BillingWebhookEvent::latest()->limit(20)->get(),
        ]);
    }

    public function trigger(Request $request, GHLService $ghl)
    {
        $request->validate([
            'event_name' => 'required|in:' . implode(',', array_keys($this->events)),
        ]);

        $user = $request->user();
        $eventName = $request->event_name;
        $subscription = $this->sampleSubscription($user, $eventName);

        $event = $ghl->sendBillingEvent($eventName, $user, $subscription, [
            'source' => 'manual_webhook_test_buttons',
            'is_test' => true,
            'test_triggered_by_user_id' => $user->id,
            'test_triggered_at' => Carbon::now()->toDateTimeString(),
        ]);

        $message = $event->status === 'sent'
            ? "{$this->events[$eventName]} test webhook sent."
            : "{$this->events[$eventName]} test webhook logged as {$event->status}.";

        return redirect()
            ->route('webhook.buttons')
            ->with($event->status === 'sent' ? 'success' : 'warning', $message);
    }

    protected function sampleSubscription($user, $eventName)
    {
        $status = in_array($eventName, ['subscription_cancelled', 'trial_limit_reached'])
            ? 'inactive'
            : 'active';

        return new StripeCustomer([
            'user_id' => $user->id,
            'stripe_customer_id' => 'cus_test_' . $user->id,
            'stripe_payment_method_id' => 'pm_test_manual',
            'subscription_id' => 'sub_test_' . $eventName,
            'plan_id' => config('services.ghl.free_trial_plan_id', 'free_trial_30_days'),
            'status' => $status,
            'next_billing_date' => Carbon::now()->addMonth(),
        ]);
    }
}
