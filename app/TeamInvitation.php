<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
    protected $fillable = [
        'team_group_id',
        'invited_by',
        'email',
        'token',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected $dates = [
        'expires_at',
        'accepted_at',
    ];

    public function group()
    {
        return $this->belongsTo(TeamGroup::class, 'team_group_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isPending()
    {
        return $this->status === 'pending' && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
