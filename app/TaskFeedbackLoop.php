<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskFeedbackLoop extends Model
{
    protected $fillable = [
        'task_id',
        'project_id',
        'user_id',
        'resolved_by',
        'feedback',
        'status',
        'resolved_at',
    ];

    protected $dates = [
        'resolved_at',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
