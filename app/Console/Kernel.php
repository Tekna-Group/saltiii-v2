<?php

namespace App\Console;

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
            $subs = \App\StripeCustomer::where('status', 'active')->get();
    
            foreach ($subs as $sub) {
                if (Carbon::parse($sub->next_billing_date)->lt(Carbon::now())) {
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
