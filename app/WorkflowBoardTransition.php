<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkflowBoardTransition extends Model
{
    //
     protected $fillable = [
        'project_id',
        'from_board_id',
        'to_board_id',
        'is_allowed',
    ];
}
