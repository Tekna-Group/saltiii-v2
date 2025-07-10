
<?php
use App\Task;


function taskDue() {

    $tasks = Task::where('user_id',auth()->user()->id)->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('completed',0)->where('due_date','<',date('Y-m-d'))->count();
        
    return $tasks;
}