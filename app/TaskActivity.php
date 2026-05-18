<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    protected $fillable = [
        'project_id',
        'task_id',
        'activity',
        'hours',
        'created_by',
        'user_id',
        'date',
        'timekeeping_from',
        'timekeeping_to',
        'generated_by',
        'comments',
        'file',
        'payment_method',
        'payment_reference',
        'payment_amount',
        'paid_at',
        'payment_approved_by',
    ];

    protected $dates = ['date', 'timekeeping_from', 'timekeeping_to', 'paid_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
