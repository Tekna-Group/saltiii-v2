<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use App\User;
use App\StripeCustomer;
use App\Invoice;
use App\Services\GHLService;
use Carbon\Carbon;

class BillingController extends Controller
{
    //
    public function subscribe(Request $request)
    {
        $user = auth()->user();

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // 1. Create Stripe customer if not existing
            $previousStatus = $user->stripeCustomer ? $user->stripeCustomer->status : null;
            $returnedWithinThreeDays = $user->stripeCustomer
                && in_array($previousStatus, ['inactive', 'cancelled', 'canceled'])
                && $user->stripeCustomer->updated_at
                && Carbon::parse($user->stripeCustomer->updated_at)->diffInDays(Carbon::now()) <= 3;

            if (!$user->stripeCustomer || !$user->stripeCustomer->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name'  => $user->name,
                    'payment_method' => $request->payment_method_id,
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method_id
                    ]
                ]);

                $stripeCustomer = StripeCustomer::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'stripe_customer_id' => $customer->id,
                        'stripe_payment_method_id' => $request->payment_method_id,
                    ]
                );

                $user->setRelation('stripeCustomer', $stripeCustomer);
            } else {
                $customer = Customer::retrieve($user->stripeCustomer->stripe_customer_id);
            }

            // 2. Create Subscription
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => 'price_12345', // Replace with your Stripe Plan/Price ID
                ]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Update subscription record
            $user->stripeCustomer->subscription_id = $subscription->id;
            $user->stripeCustomer->plan_id = 'price_12345';
            $user->stripeCustomer->status = $subscription->status;
            $user->stripeCustomer->next_billing_date = Carbon::now()->addMonth();
            $user->stripeCustomer->save();

            // Create local invoice
            $invoice = new Invoice();
            $invoice->user_id = $user->id;
            $invoice->invoice_number = 'INV-' . time();
            $invoice->service = 'Monthly Subscription';
            $invoice->description = 'Subscription for premium plan';
            $invoice->amount = 49.99; // Example amount
            $invoice->paid_status = $subscription->status === 'active' ? 'paid' : 'pending';
            $invoice->payment_type = 'card';
            $invoice->payment_description = $subscription->id;
            $invoice->paid_on = now();
            $invoice->save();

            $ghl = app(GHLService::class);

            if (!$ghl->hasSentBillingEvent($user, 'paid_subscriber', $user->stripeCustomer->subscription_id)) {
                $ghl->sendBillingEvent('paid_subscriber', $user, $user->stripeCustomer, [
                    'amount' => $invoice->amount,
                    'source' => 'billing_controller',
                ]);
            }

            if ($previousStatus && in_array($previousStatus, ['inactive', 'cancelled', 'canceled'])) {
                $ghl->sendBillingEvent('reactivated_after_cancellation', $user, $user->stripeCustomer, [
                    'previous_status' => $previousStatus,
                    'source' => 'billing_controller',
                ]);
            }

            if ($returnedWithinThreeDays) {
                $ghl->sendBillingEvent('returned_within_3_days', $user, $user->stripeCustomer, [
                    'previous_status' => $previousStatus,
                    'source' => 'billing_controller',
                ]);
            }

            return response()->json([
                'status' => 'success',
                'subscription_id' => $subscription->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
