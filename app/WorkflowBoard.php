<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkflowBoard extends Model
{
    //
    protected $fillable = ['project_id', 'board_id', 'name'];

    public function statuses()
    {
        return $this->hasMany(WorkflowStatus::class);
    }

    public function transitions()
    {
        return $this->hasMany(WorkflowTransition::class);
    }
}
