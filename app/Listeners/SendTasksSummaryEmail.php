<?php

namespace App\Listeners;

use App\Events\TasksSummaryGenerated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\TasksSummaryMail;
use Illuminate\Support\Facades\Mail;

class SendTasksSummaryEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TasksSummaryGenerated  $event
     * @return void
     */
    public function handle(TasksSummaryGenerated $event)
    {
        //
           Mail::to($event->user->email)->send(new TasksSummaryMail($event->user, $event->tasks));
    }
}
