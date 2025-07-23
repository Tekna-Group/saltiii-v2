<?php

namespace App\Http\Controllers;
use App\Task;
use App\TaskUser;
use App\Project;
use App\TaskComment;
use App\ProjectBoard;
use App\TaskActivity;
use App\TaskAttachment;
use App\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
class TaskController extends Controller
{
    //
    public function index()
    {
        // Fetch all tasks from the database
        $tasks = Task::with(['users', 'project', 'comments', 'attachments'])->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
        $projects = Project::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
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
    public function changeStatus(Request $request)
    {
        // dd($request->all());
        $task = Task::findOrfail($request->task_id);
        $task->project_board_id = $request->column_id;
        $task->save();

        return response()->json(['message' => 'Task updated successfully','data' => $task]);
    }
    public function view($id)
    {
        // Fetch the task by ID
        $task = Task::with(['users', 'project', 'comments', 'attachments'])->findOrFail($id);
        $boards = ProjectBoard::where('project_id',$task->project_id)->get();
        // Return the view with the task data
        return view('tasks.view', ['task' => $task,
        'boards' => $boards
        ]);
    }
    public function comment(Request $request,$id)
    {
        $task = Task::findOrfail($id);
        $TaskComment = new TaskComment();
        $TaskComment->comment = $request->comment;
        $TaskComment->task_id = $id;
        $TaskComment->project_id = $task->project_id;
        $TaskComment->user_id = auth()->user()->id;
        $TaskComment->save();

        Alert::success('Successfully Posted')->persistent('Dismiss');
        return back();
    }

    public function attachment(Request $request,$id)
    
    {
        $task = Task::findOrfail($id);
        $TaskActivity = new TaskAttachment();
        $TaskActivity->project_id = $task->project_id;
        $TaskActivity->task_id = $task->id;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $sizeInBytes = $file->getSize();

             // Optional: Convert to KB or MB        // kilobytes
            $sizeInMB = round($sizeInBytes / 1048576, 2);
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tasks'), $filename);
            $TaskActivity->file_type = $file->getClientOriginalExtension();
            $TaskActivity->file = 'uploads/tasks/' . $filename;
            $TaskActivity->name = $file->getClientOriginalName();
           
            $TaskActivity->file_size =  $sizeInMB;      // megabytes
        
        }
        $TaskActivity->user_id = auth()->user()->id;
        $TaskActivity->save();

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
        return back();
    }
    public function activity (Request $request,$id)
    {
        // dd($request->all());
        $task = Task::findOrfail($id);
        $TaskActivity = new TaskActivity();
        $TaskActivity->activity = $request->task;
        $TaskActivity->task_id = $id;
        $TaskActivity->project_id = $task->project_id;
        $TaskActivity->user_id = auth()->user()->id;
        $TaskActivity->created_by = auth()->user()->id;
        $TaskActivity->hours = $request->hours;
        $TaskActivity->date = $request->date;
        $TaskActivity->save();

        Alert::success('Successfully Encoded')->persistent('Dismiss');
        return back();
    }
     public function complete($id)
    {
        $project = Task::findOrFail($id);
        $project->completed = 1;
        $project->save();
        Alert::success('Task marked as completed.')->persistent('Dismiss');
        return back();
    }
     public function changeStatusManual(Request $request,$id)
    {
        // dd($request->all());
        $task = Task::findOrfail($id);
        $task->project_board_id = $request->project_board_id;
        $task->save();
        Alert::success('Task updated successfully')->persistent('Dismiss');
        return back();
    }
}
