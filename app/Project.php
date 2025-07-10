<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'completed', 
        'user_id', // add this line
    ];
    protected $casts = [
        'completed' => 'boolean',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users'); // if many-to-many
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function statuses()
    {
        return $this->hasMany(ProjectBoard::class);
    }
    public function activities()
    {
        return $this->hasMany(TaskActivity::class);
    }
}
