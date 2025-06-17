<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id');
    }

    public function board()
    {
         return $this->belongsTo(ProjectBoard::class,'project_board_id','id');
    }

    // Task belongs to one Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // One-to-many relation: Task has many comments
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    // One-to-many relation: Task has many attachments
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
    public function activities()
    {
        return $this->hasMany(TaskActivity::class);
    }
}
