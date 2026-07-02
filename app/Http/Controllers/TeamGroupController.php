<?php

namespace App\Http\Controllers;

use App\TeamGroup;
use App\TeamGroupMember;
use App\TeamInvitation;
use App\Mail\TeamInvitationMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class TeamGroupController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $groups = TeamGroup::with(['owner', 'billingUser', 'members.user', 'invitations.inviter'])
            ->where('owner_id', $user->id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('name', 'asc')
            ->get();

        $activeGroup = $request->filled('group')
            ? $groups->firstWhere('id', (int) $request->group)
            : null;

        $activeGroup = $activeGroup ?: ($groups->firstWhere('owner_id', $user->id) ?: $groups->first());

        return view('team-groups.index', compact('groups', 'activeGroup'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = TeamGroup::create([
            'owner_id' => auth()->id(),
            'billing_user_id' => auth()->id(),
            'name' => $request->name,
        ]);

        TeamGroupMember::firstOrCreate(
            ['team_group_id' => $group->id, 'user_id' => auth()->id()],
            ['role' => 'owner', 'joined_at' => now()]
        );

        Alert::success('Group created.')->persistent('Dismiss');
        return redirect()->route('team-groups.index');
    }

    public function invite(Request $request, $groupId)
    {
        $group = $this->ownedGroup($groupId);

        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $invitation = TeamInvitation::updateOrCreate(
            [
                'team_group_id' => $group->id,
                'email' => strtolower($request->email),
                'status' => 'pending',
            ],
            [
                'invited_by' => auth()->id(),
                'token' => Str::random(48),
                'expires_at' => Carbon::now()->addDays(14),
            ]
        );

        $inviteLink = route('team-groups.invite.show', $invitation->token);
        $mailSent = $this->sendInvitationEmail($invitation, $inviteLink);

        Alert::success($mailSent ? 'Invite email sent.' : 'Invite link created, but email could not be sent.')->persistent('Dismiss');
        return back()
            ->with('invite_link', $inviteLink)
            ->with('invite_mail_sent', $mailSent);
    }

    public function resendInvite($groupId, $invitationId)
    {
        $group = $this->ownedGroup($groupId);
        $invitation = TeamInvitation::where('team_group_id', $group->id)->findOrFail($invitationId);

        if (!$invitation->isPending()) {
            Alert::warning('Only pending invitations can be resent.')->persistent('Dismiss');
            return back();
        }

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            $invitation->update([
                'expires_at' => Carbon::now()->addDays(14),
                'status' => 'pending',
            ]);
        }

        $inviteLink = route('team-groups.invite.show', $invitation->token);
        $mailSent = $this->sendInvitationEmail($invitation, $inviteLink);

        Alert::success($mailSent ? 'Invite email resent.' : 'Invite email could not be sent.')->persistent('Dismiss');
        return back()
            ->with('invite_link', $inviteLink)
            ->with('invite_mail_sent', $mailSent);
    }

    public function showInvite($token)
    {
        $invitation = TeamInvitation::with('group.owner')
            ->where('token', $token)
            ->firstOrFail();

        return view('team-groups.invite', compact('invitation'));
    }

    public function join(Request $request, $token)
    {
        $invitation = TeamInvitation::with('group')->where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('team-groups.index')->withErrors(['invite' => 'This invitation is no longer available.']);
        }

        if (strtolower(auth()->user()->email) !== strtolower($invitation->email)) {
            return back()->withErrors(['invite' => 'This invite was sent to '.$invitation->email.'. Please log in with that email.']);
        }

        $this->acceptInvitation($invitation, auth()->id());

        Alert::success('You joined '.$invitation->group->name.'.')->persistent('Dismiss');
        return redirect()->route('team-groups.index');
    }

    public static function acceptInvitation(TeamInvitation $invitation, $userId)
    {
        TeamGroupMember::firstOrCreate(
            ['team_group_id' => $invitation->team_group_id, 'user_id' => $userId],
            ['role' => 'member', 'joined_at' => now()]
        );

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    private function ownedGroup($groupId)
    {
        return TeamGroup::where('owner_id', auth()->id())->findOrFail($groupId);
    }

    private function sendInvitationEmail(TeamInvitation $invitation, $inviteLink)
    {
        try {
            $invitation->load(['group', 'inviter']);
            Mail::to($invitation->email)->send(new TeamInvitationMail($invitation, $inviteLink));
            return true;
        } catch (\Exception $e) {
            Log::error('Team invitation email failed: '.$e->getMessage(), [
                'team_invitation_id' => $invitation->id,
                'email' => $invitation->email,
            ]);

            return false;
        }
    }
}
