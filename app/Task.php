<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }

    // Task belongs to one Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // One-to-many relation: Task has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // One-to-many relation: Task has many attachments
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
