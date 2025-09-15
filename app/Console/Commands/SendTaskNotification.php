<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Task;
use Carbon\Carbon;
use Mail;

class SendTaskNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send task notifications email to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
         $today = Carbon::today();

        // Example: Send notifications to all active users
        $users = User::where('status', 'active')->get();

        foreach ($users as $user) {
            $tasks = [
                'delayed'   => Task::where('user_id', $user->id)
                                    ->where('due_date', '<', $today)
                                    ->where('completed', 0)
                                    ->get(),

                'due_today' => Task::where('user_id', $user->id)
                                    ->whereDate('due_date', $today)
                                    ->where('completed', 0)
                                    ->get(),

                'upcoming'  => Task::where('user_id', $user->id)
                                    ->whereDate('due_date', '>', $today)
                                    ->whereDate('due_date', '<=', $today->copy()->addDays(7))
                                    ->where('completed', 0)
                                    ->get(),
            ];

            // Only send email if there are tasks to notify
            if (
                $tasks['delayed']->count() ||
                $tasks['due_today']->count() ||
                $tasks['upcoming']->count()
            ) {
                Mail::send('emails.tasks_summary', compact('user', 'tasks'), function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Task Notification Summary');
                });
            }
        }

        $this->info('Task notification emails sent successfully.');
    }
}
