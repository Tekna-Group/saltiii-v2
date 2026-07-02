<?php

namespace App\Mail;

use App\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $inviteLink;

    public function __construct(TeamInvitation $invitation, $inviteLink)
    {
        $this->invitation = $invitation;
        $this->inviteLink = $inviteLink;
    }

    public function build()
    {
        return $this->subject('You are invited to join '.$this->invitation->group->name.' on SALTIII')
            ->view('emails.team_invitation');
    }
}
