<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkflowStatus extends Model
{
    //
    protected $fillable = ['workflow_board_id', 'name', 'order'];
}
