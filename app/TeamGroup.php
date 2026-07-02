<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeamGroup extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'billing_user_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function billingUser()
    {
        return $this->belongsTo(User::class, 'billing_user_id');
    }

    public function members()
    {
        return $this->hasMany(TeamGroupMember::class);
    }

    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }
}
