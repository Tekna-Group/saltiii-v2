<?php

namespace App\Http\Controllers;
use App\Project;
use App\ProjectBoard;
use App\Task;
use App\TaskActivity;
use App\User;
use Illuminate\Support\Facades\DB;
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
        $boards = ProjectBoard::get();
        $query = Project::with([
            'activities',
            'comments' => function($q) {
                $q->orderBy('updated_at', 'desc');
            }
        ])
        ->where('completed', 0)
        ->leftJoin(DB::raw('(SELECT project_id, MAX(updated_at) as latest_comment_updated_at FROM task_comments GROUP BY project_id) as c'), 'projects.id', '=', 'c.project_id')
        ->orderBy('c.latest_comment_updated_at', 'desc')
        ->select('projects.*', 'c.latest_comment_updated_at');
        // dd($query->get());
    
        if (auth()->user()->role != 'Admin') {
            $query->with(['activities' => function ($q) {
                    $q->where('user_id', auth()->id());
                }])
                ->whereHas('users', function ($q) {
                    $q->where('users.id', auth()->id());
                });
        }
        
        $projects = $query->get();
        $tasks = Task::where('completed',0)->orderBy('due_date','asc')->get();
        // if(auth()->user()->role != 'Admin') {
            $tasks = $tasks->filter(function ($task) {
                return $task->users->contains(auth()->user()->id);
            });
        // }
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
        // $project_this_week = Project::with(['activities' => function ($query) use ($last_sunday, $saturday) {
        // $query->whereBetween('date', [$last_sunday, $saturday]);
        // }])
        // ->get()
        // ->map(function ($project) {
        //     return [
        //         'title' => $project->name,
        //         'hours' => number_format($project->activities->sum('hours'), 2, '.', '') // ✅ 2 decimals
        //     ];
        // })
        // ->filter(fn($item) => $item['hours'] > 0)
        // ->sortByDesc('hours')
        // ->values();
        //  if(auth()->user()->role != 'Admin') {
        //    $project_this_week = Project::with(['activities' => function ($query) use ($last_sunday, $saturday) {
        //             $query->whereBetween('date', [$last_sunday, $saturday])
        //                 ->where('user_id', auth()->user()->id);
        //         }])
        //         ->get()
        //         ->map(function ($project) {
        //             return [
        //                 'title' => $project->name,
        //                 'hours' => $project->activities->sum('hours')
        //             ];
        //         })
        //         ->filter(fn($item) => $item['hours'] > 0) // remove projects with 0 hours
        //         ->sortByDesc('hours')
        //         ->values();
        //  }

        // $projects_data = $projects->map(function ($project) {
        //         return [
        //             'title' => $project->name,
        //             'hours' => $project->activities->sum('hours')
        //         ];
        //     })->sortByDesc('hours')
        //     ->filter(fn($item) => $item['hours'] > 0) // Sort by hours in descending order
        //     ->values();
        // dd($task_due);
        return view('home',
            array(
                'projects' => $projects,
                'tasks' => $tasks,
                'activities' => $activities,
                'members' => $members,
                'last_sunday' => $last_sunday,
                'saturday' => $saturday,
                'boards' => $boards,
                // 'projects_data' => $projects_data,
                // 'totalHours' => $projects_data->sum('hours'),
                // 'project_this_week' => $project_this_week,

            )
        );
    }
}
