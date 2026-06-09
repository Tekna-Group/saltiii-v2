<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use App\StripeCustomer;
use App\Invoice;
use App\Services\GHLService;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
class SubscriptionController extends Controller
{
    public function showForm()
    {
        return view('subscribe');
    }

    public function subscribe(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'email' => 'required|email',
            'name' => 'required|string',
            'payment_method' => 'required|string',
            'plan_id' => 'required|string',
            'amount' => 'required|numeric'
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));
    

        // 1️⃣ Create or fetch Stripe Customer
        $existing = StripeCustomer::where('user_id', $request->user_id)->first();
        $previousStatus = $existing ? $existing->status : null;
        $returnedWithinThreeDays = $existing
            && in_array($previousStatus, ['inactive', 'cancelled', 'canceled'])
            && $existing->updated_at
            && Carbon::parse($existing->updated_at)->diffInDays(Carbon::now()) <= 3;

        if (!$existing || !$existing->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $request->email,
                'name' => $request->name,
                'payment_method' => $request->payment_method,
                'invoice_settings' => ['default_payment_method' => $request->payment_method]
            ]);

            $existing = StripeCustomer::updateOrCreate(
                ['user_id' => $request->user_id],
                [
                    'stripe_customer_id' => $customer->id,
                ]
            );
        }

        // 2️⃣ Create subscription in Stripe
        $subscription = Subscription::create([
            'customer' => $existing->stripe_customer_id,
            'items' => [['price' => $request->plan_id]],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        // 3️⃣ Save subscription locally
        $existing->update([
            'subscription_id' => $subscription->id,
            'plan_id' => $request->plan_id,
            'status' => $subscription->status,
            'next_billing_date' => Carbon::now()->addMonth(),
        ]);

        // 4️⃣ Save invoice
        Invoice::create([
            'user_id' => $request->user_id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'amount' => $request->amount,
            'status' => 'Paid',
            'service' => 'Basic Plan Subscription',
            'description' => 'Subscription payment for plan ID: ' . $request->plan_id,
            'paid_status' => 'Paid',
            'payment_type' => 'stripe',
            'payment_description' => $subscription->latest_invoice->id,
            'paid_on' => Carbon::now(),
        ]);

        $existing->load('user');
        $ghl = app(GHLService::class);

        if (!$ghl->hasSentBillingEvent($existing->user, 'paid_subscriber', $existing->subscription_id)) {
            $ghl->sendBillingEvent('paid_subscriber', $existing->user, $existing, [
                'amount' => $request->amount,
                'source' => 'subscription_controller',
            ]);
        }

        if ($previousStatus && in_array($previousStatus, ['inactive', 'cancelled', 'canceled'])) {
            $ghl->sendBillingEvent('reactivated_after_cancellation', $existing->user, $existing, [
                'previous_status' => $previousStatus,
                'source' => 'subscription_controller',
            ]);
        }

        if ($returnedWithinThreeDays) {
            $ghl->sendBillingEvent('returned_within_3_days', $existing->user, $existing, [
                'previous_status' => $previousStatus,
                'source' => 'subscription_controller',
            ]);
        }

        Alert::success('Subscription successful! You now have full access to the system.')->persistent('Dismiss');
        return redirect('/dashboard');
    }

    public function showPlan()
    {
        return view('subscription_plan');
    }
}
