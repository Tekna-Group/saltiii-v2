<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkflowBoardAssignee extends Model
{


    // Fillable fields for mass assignment
    protected $fillable = [
        'project_id',
        'board_id',
        'user_id',
        'replace_existing',
        'fallback_rule'
    ];
}
