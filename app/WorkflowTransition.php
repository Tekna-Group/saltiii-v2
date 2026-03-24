<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkflowTransition extends Model
{
    //
    protected $fillable = [
        'workflow_board_id',
        'from_status_id',
        'to_status_id',
        'is_allowed'
    ];
}
