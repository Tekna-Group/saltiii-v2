<?php

namespace App\Http\Controllers;
use App\Project;
use App\Task;
use App\TaskActivity;
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
        $projects = Project::get();
        $tasks = Task::get();
        $activities = TaskActivity::get();
        // dd($task_due);
        return view('home',
            array(
                'projects' => $projects,
                'tasks' => $tasks,
                'activities' => $activities,

            )
        );
    }
}
