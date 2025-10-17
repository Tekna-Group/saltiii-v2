<?php

namespace App\Http\Middleware;

use Closure;
use App\StripeCustomer;
use Carbon\Carbon;

class CheckSubscription
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        $sub = StripeCustomer::where('user_id', $user->id)->first();

        if (!$sub || $sub->status != 'active' || Carbon::parse($sub->next_billing_date)->lt(Carbon::now())) {
            return redirect('/subscribe')->with('error', 'Your subscription is inactive or expired. Please subscribe to continue.');
        }

        return $next($request);
    }
}
