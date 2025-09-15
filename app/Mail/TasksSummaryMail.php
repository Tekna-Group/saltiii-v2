<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TasksSummaryMail extends Mailable
{   
    use Queueable, SerializesModels;
    public $user;
    public $tasks;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $tasks)
    {
        //
           $this->user = $user;
        $this->tasks = $tasks;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         return $this->subject('Daily Task Summary')
                    ->view('emails.tasks_summary');
    }
}
