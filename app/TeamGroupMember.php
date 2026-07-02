<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeamGroupMember extends Model
{
    protected $fillable = [
        'team_group_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $dates = [
        'joined_at',
    ];

    public function group()
    {
        return $this->belongsTo(TeamGroup::class, 'team_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
