<?php

namespace App\Http\Controllers;

use App\Services\GHLService;
use App\StripeCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, GHLService $ghl)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook.secret');

        try {
            $event = $secret
                ? Webhook::constructEvent($payload, $signature, $secret, config('services.stripe.webhook.tolerance', 300))
                : json_decode($payload);
        } catch (\Exception $e) {
            \Log::warning('Stripe webhook verification failed', ['message' => $e->getMessage()]);

            return response()->json(['message' => 'Invalid Stripe webhook payload.'], 400);
        }

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event, $ghl);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionCancelled($event, $ghl);
                break;
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event, $ghl);
                break;
        }

        return response()->json(['received' => true]);
    }

    protected function handlePaymentSucceeded($event, GHLService $ghl)
    {
        $invoice = $event->data->object;
        $subscription = $this->findSubscription($invoice->customer, $invoice->subscription);

        if (!$subscription || !$subscription->user) {
            return;
        }

        $subscription->status = 'active';
        if (isset($invoice->lines->data[0]->period->end)) {
            $subscription->next_billing_date = Carbon::createFromTimestamp($invoice->lines->data[0]->period->end);
        }
        $subscription->save();

        $billingReason = isset($invoice->billing_reason) ? $invoice->billing_reason : null;
        $amountPaid = isset($invoice->amount_paid) ? $invoice->amount_paid / 100 : null;

        if ($billingReason === 'subscription_cycle') {
            if (!$ghl->hasSentBillingEvent($subscription->user, 'renewed_past_first_billing_cycle', $subscription->subscription_id)) {
                $ghl->sendBillingEvent('renewed_past_first_billing_cycle', $subscription->user, $subscription, [
                    'amount' => $amountPaid,
                    'stripe_invoice_id' => $invoice->id,
                    'source' => 'stripe_webhook',
                ]);
            }

            return;
        }

        if (!$ghl->hasSentBillingEvent($subscription->user, 'paid_subscriber', $subscription->subscription_id)) {
            $ghl->sendBillingEvent('paid_subscriber', $subscription->user, $subscription, [
                'amount' => $amountPaid,
                'stripe_invoice_id' => $invoice->id,
                'source' => 'stripe_webhook',
            ]);
        }
    }

    protected function handleSubscriptionCancelled($event, GHLService $ghl)
    {
        $stripeSubscription = $event->data->object;
        $subscription = $this->findSubscription($stripeSubscription->customer, $stripeSubscription->id);

        if (!$subscription || !$subscription->user) {
            return;
        }

        $subscription->status = 'inactive';
        $subscription->save();

        $ghl->sendBillingEvent('subscription_cancelled', $subscription->user, $subscription, [
            'stripe_status' => isset($stripeSubscription->status) ? $stripeSubscription->status : null,
            'cancelled_at' => isset($stripeSubscription->canceled_at)
                ? Carbon::createFromTimestamp($stripeSubscription->canceled_at)->toDateTimeString()
                : Carbon::now()->toDateTimeString(),
            'source' => 'stripe_webhook',
        ]);
    }

    protected function handleSubscriptionUpdated($event, GHLService $ghl)
    {
        $stripeSubscription = $event->data->object;
        $subscription = $this->findSubscription($stripeSubscription->customer, $stripeSubscription->id);

        if (!$subscription || !$subscription->user) {
            return;
        }

        $previousStatus = isset($event->data->previous_attributes->status)
            ? $event->data->previous_attributes->status
            : $subscription->status;

        $lastStatusChangedAt = $subscription->updated_at;

        $subscription->status = $stripeSubscription->status;
        if (isset($stripeSubscription->current_period_end)) {
            $subscription->next_billing_date = Carbon::createFromTimestamp($stripeSubscription->current_period_end);
        }
        $subscription->save();

        if ($stripeSubscription->status === 'active' && in_array($previousStatus, ['inactive', 'cancelled', 'canceled'])) {
            $ghl->sendBillingEvent('reactivated_after_cancellation', $subscription->user, $subscription, [
                'previous_status' => $previousStatus,
                'source' => 'stripe_webhook',
            ]);

            if ($lastStatusChangedAt && Carbon::parse($lastStatusChangedAt)->diffInDays(Carbon::now()) <= 3) {
                $ghl->sendBillingEvent('returned_within_3_days', $subscription->user, $subscription, [
                    'previous_status' => $previousStatus,
                    'source' => 'stripe_webhook',
                ]);
            }
        }
    }

    protected function findSubscription($stripeCustomerId, $stripeSubscriptionId = null)
    {
        $query = StripeCustomer::with('user')->where('stripe_customer_id', $stripeCustomerId);

        if ($stripeSubscriptionId) {
            $query->where('subscription_id', $stripeSubscriptionId);
        }

        return $query->first();
    }
}
