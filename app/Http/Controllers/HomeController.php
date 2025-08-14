<?php

namespace App\Http\Controllers;
use App\Project;
use App\Task;
use App\TaskActivity;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $projects = Project::with('activities')->where('completed',0)->get();
        if(auth()->user()->role != 'Admin') {
            $projects = $projects->with(['activities' => function ($query) {
                $query->where('user_id', auth()->id());
            }])->filter(function ($project) {
                return $project->users->contains(auth()->user()->id);
            });
        }
        $tasks = Task::where('completed',0)->get();
        if(auth()->user()->role != 'Admin') {
            $tasks = $tasks->filter(function ($task) {
                return $task->users->contains(auth()->user()->id);
            });
        }
        $last_sunday = date('Y-m-d',strtotime('last sunday'));
        $saturday = date("Y-m-d", strtotime("+6 days",strtotime($last_sunday)));
        
        $activities = TaskActivity::get();
        if(auth()->user()->role != 'Admin') {
            $activities = $activities->filter(function ($activity) {
                return $activity->user_id == auth()->user()->id;
            });
        }
        $members = User::with(['activities' => function ($query) use ($last_sunday, $saturday) {
            $query->whereBetween('date', [$last_sunday, $saturday]);
        }])->get();

        $projects_data = $projects->map(function ($project) {
                return [
                    'title' => $project->name,
                    'hours' => $project->activities->sum('hours')
                ];
            })->sortByDesc('hours') // Sort by hours in descending order
            ->values();;
        // dd($task_due);
        return view('home',
            array(
                'projects' => $projects,
                'tasks' => $tasks,
                'activities' => $activities,
                'members' => $members,
                'last_sunday' => $last_sunday,
                'saturday' => $saturday,
                'projects_data' => $projects_data,
                'totalHours' => $projects_data->sum('hours'),

            )
        );
    }
}
