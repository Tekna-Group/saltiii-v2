<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserTaggedNotification extends Notification
{
    use Queueable;
       protected $tagger;
    protected $comment;
    protected $task;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    
    /**
     * Create a new notification instance.
     *
     * @param  \App\User  $tagger
     * @param  \App\TaskComment  $comment
     * @param  \App\Task  $task
     */
     public function __construct($tagger, $comment, $task)
    {
        $this->tagger = $tagger;
        $this->comment = $comment;
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */

     public function toDatabase($notifiable)
    {
        return [
            'tagger_id' => $this->tagger->id,
            'tagger_name' => $this->tagger->name,
            'comment_id' => $this->comment->id,
            'comment_text' => $this->comment->comment,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'message' => "{$this->tagger->name} mentioned you in a comment.",
        ];
    }
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You were mentioned in a comment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("{$this->tagger->name} mentioned you in a comment on the task: \"{$this->task->title}\".")
            ->line("Comment: \"{$this->comment->comment}\"")
            ->action('View Task', url('/view-project/view-task/' . $this->task->id))
            ->line('Thank you for staying engaged and collaborating with your team!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
