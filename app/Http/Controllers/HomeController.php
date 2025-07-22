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
        $projects = Project::where('completed',0)->get();
        $tasks = Task::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
        
        $last_sunday = date('Y-m-d',strtotime('last sunday'));
        $saturday = date("Y-m-d", strtotime("+6 days",strtotime($last_sunday)));
        
        $activities = TaskActivity::where('user_id',auth()->user()->id)->whereBetween('date', [$last_sunday, $saturday])->get();
        $members = User::with(['activities' => function ($query) use ($last_sunday, $saturday) {
            $query->whereBetween('date', [$last_sunday, $saturday]);
        }])->get();
        // dd($task_due);
        return view('home',
            array(
                'projects' => $projects,
                'tasks' => $tasks,
                'activities' => $activities,
                'members' => $members,
                'last_sunday' => $last_sunday,
                'saturday' => $saturday,

            )
        );
    }
}
