<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         \App\Console\Commands\SendTaskNotification::class,
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

         $schedule->command('tasks:notify')
        ->weekdays()
        ->twiceDaily(20, 22)
        ->timezone('Asia/Manila')
        ->withoutOverlapping();
        $schedule->call(function () {
            $subs = \App\StripeCustomer::with('user')->where('status', 'active')->get();
            $ghl = app(\App\Services\GHLService::class);
    
            foreach ($subs as $sub) {
                if (Carbon::parse($sub->next_billing_date)->lt(Carbon::now())) {
                    if (
                        $sub->user
                        && $sub->plan_id === config('services.ghl.free_trial_plan_id', 'free_trial_30_days')
                        && !$ghl->hasSentBillingEvent($sub->user, 'trial_limit_reached', $sub->subscription_id)
                    ) {
                        $ghl->sendBillingEvent('trial_limit_reached', $sub->user, $sub, [
                            'source' => 'daily_subscription_check',
                        ]);
                    }

                    $sub->update(['status' => 'inactive']);
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
