<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = [
        'name',
        'parent_id',
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

    public function parent()
    {
        return $this->belongsTo(Project::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Project::class, 'parent_id');
    }

    public function subprojects()
    {
        return $this->children();
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
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
