<?php

namespace App\Http\Controllers;
use App\Task;
use App\TaskUser;
use App\Project;
use App\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
class TaskController extends Controller
{
    //
    public function index()
    {
        // Fetch all tasks from the database
        $tasks = Task::with(['users', 'project', 'comments', 'attachments'])->get();
        $projects = Project::get();
        $users = User::get();
        // Return the view with the tasks data
        return view('tasks.index', ['tasks' => $tasks,
                                    'projects' => $projects,
                                    'users' => $users,
                                    ]); 
    }
    public function store(Request $request, $project_id)
    {
        // dd($request->all());
        // Validate the request data
       

        // Create a new task instance
        $task = new Task();
        $task->project_id = $project_id;
        $task->title = $request->input('task');
        $task->description = $request->input('description');
        $task->due_date = $request->input('dueDate');
        $task->priority = $request->input('priority');
        $task->project_board_id = $request->input('taskColumn'); // Assuming status is the ID of the project board
        $task->user_id = auth()->user()->id; // Assuming the task is created by the authenticated user
        $task->save();

        foreach($request->input('team_member') as $memberId) {
            $projectUser = new TaskUser();
            $projectUser->task_id = $task->id;
            $projectUser->user_id = $memberId;
            $projectUser->save();
        }

        // Redirect back with success message
        Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }
    public function view($id)
    {
        // Fetch the task by ID
        $task = Task::with(['users', 'project', 'comments', 'attachments'])->findOrFail($id);
        // Return the view with the task data
        return view('tasks.view', ['task' => $task]);
    }
}
