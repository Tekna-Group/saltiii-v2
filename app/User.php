<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email_verified_at', 'google_id', 'wallet_address', 'wallet_network', 'stripe_account_id', 'airwallex_beneficiary_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id');
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_users', 'user_id', 'task_id');
    }
    public function activities()
    {
        return $this->hasMany(TaskActivity::class);
    }
    public function salary()
    {
        return $this->hasOne(UserSalary::class);
    }
    public function hourlySalary()
    {
        return $this->hasOne(UserSalary::class)->where('type', 'hourly');
    }
    public function stripeCustomer()
    {
        return $this->hasOne(\App\StripeCustomer::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function ownedTeamGroups()
    {
        return $this->hasMany(TeamGroup::class, 'owner_id');
    }
    public function teamGroupMemberships()
    {
        return $this->hasMany(TeamGroupMember::class);
    }
    public function teamGroups()
    {
        return $this->belongsToMany(TeamGroup::class, 'team_group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }
    public function billingTeamGroups()
    {
        return $this->hasMany(TeamGroup::class, 'billing_user_id');
    }

    public static function assignableFor(User $user)
    {
        if ($user->role === 'Admin') {
            return static::orderBy('name', 'asc')->get();
        }

        $groupIds = TeamGroup::where('owner_id', $user->id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('id');

        $userIds = TeamGroupMember::whereIn('team_group_id', $groupIds)
            ->pluck('user_id')
            ->push($user->id)
            ->unique()
            ->values();

        return static::whereIn('id', $userIds)->orderBy('name', 'asc')->get();
    }
}
