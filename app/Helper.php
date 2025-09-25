
<?php
use App\Task;
use App\TaskActivity;


function taskDue() {

    $tasks = Task::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('completed',0)->where('due_date','<=',date('Y-m-d'))->count();
        
    return $tasks;
}

function hours_today()
{
    $last_sunday = date('Y-m-d',strtotime('last sunday'));
    $saturday = date("Y-m-d", strtotime("+6 days",strtotime($last_sunday)));
  
    $activities = TaskActivity::where('user_id',auth()->user()->id)->whereBetween('date', [$last_sunday, $saturday])
    ->sum('hours');

    return number_format($activities,2);
}
function notifications()
{
    $notifications = auth()->user()->unreadNotifications;
    return $notifications;
}