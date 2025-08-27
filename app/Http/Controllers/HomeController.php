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
        $query = Project::with('activities')
        ->where('completed', 0);
    
        if (auth()->user()->role != 'Admin') {
            $query->with(['activities' => function ($q) {
                    $q->where('user_id', auth()->id());
                }])
                ->whereHas('users', function ($q) {
                    $q->where('users.id', auth()->id());
                });
        }
        
        $projects = $query->get();
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
        $project_this_week = Project::with(['activities' => function ($query) use ($last_sunday, $saturday) {
        $query->whereBetween('date', [$last_sunday, $saturday]);
        }])
        ->get()
        ->map(function ($project) {
            return [
                'title' => $project->name,
                'hours' => number_format($project->activities->sum('hours'), 2, '.', '') // ✅ 2 decimals
            ];
        })
        ->filter(fn($item) => $item['hours'] > 0)
        ->sortByDesc('hours')
        ->values();
         if(auth()->user()->role != 'Admin') {
           $project_this_week = Project::with(['activities' => function ($query) use ($last_sunday, $saturday) {
        $query->whereBetween('date', [$last_sunday, $saturday])
              ->where('user_id', auth()->user()->id);
    }])
    ->get()
    ->map(function ($project) {
        return [
            'title' => $project->name,
            'hours' => $project->activities->sum('hours')
        ];
    })
    ->filter(fn($item) => $item['hours'] > 0) // remove projects with 0 hours
    ->sortByDesc('hours')
    ->values();
         }

        $projects_data = $projects->map(function ($project) {
                return [
                    'title' => $project->name,
                    'hours' => $project->activities->sum('hours')
                ];
            })->sortByDesc('hours')
            ->filter(fn($item) => $item['hours'] > 0) // Sort by hours in descending order
            ->values();
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
                'project_this_week' => $project_this_week,

            )
        );
    }
}
