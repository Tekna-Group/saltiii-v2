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
        $tasks = Task::with(['users', 'project', 'comments', 'attachments'])
        ->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->orderBy('due_date', 'asc') // ✅ Order by due_date ascending
        ->get();
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

    public function updateDueDate(Request $request, $id)
    {
        $request->validate([
            'due_date' => 'required|date'
        ]);
      
        $task = Task::findOrFail($id);

        $request->merge([
            'old_value' => $task->due_date,
            'new_value' => $request->due_date,
        ]);
        $task->due_date = $request->due_date;
        $task->save();
        
         $this->createTaskComment($request,$task->project_id, $task->id, 'Change Due Date');
        return response()->json([
            'success' => true,
            'message' => 'Due date updated successfully',
            'task' => $task
        ]);
    }
    public function myTasks(Request $request)
    {
        $boards = ProjectBoard::get();
         $tasks = Task::with(['users', 'project', 'comments', 'attachments'])
        ->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->where('completed',0)
        ->orderBy('due_date', 'asc') // ✅ Order by due_date ascending
        ->get();

        return view('tasks.myTasks', 
        ['tasks' => $tasks,
        'boards' => $boards,
                                    ]); 
    }
    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:High,Medium,Low',
        ]);

        $task = Task::findOrFail($id);
        $request->merge([
            'old_value' => $task->priority,
            'new_value' => $request->priority,
        ]);
        $this->createTaskComment($request,$task->project_id, $task->id, 'Update priority');
        $task->priority = $request->priority;
        $task->save();

        return response()->json(['success' => true]);
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

        $users = User::get();
        $projects = Project::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('completed',0)->get();
        $task = Task::with(['users', 'project', 'comments', 'attachments'])->findOrFail($id);
        $boards = ProjectBoard::where('project_id',$task->project_id)->get();
        // Return the view with the task data
        return view('tasks.view', ['task' => $task,
        'boards' => $boards,
        'users' => $users,
        'projects' => $projects,
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

    public function commentPost(Request $request,$taskId)
    {
         $request->validate([
            'comment' => 'required|string',
            'proof'   => 'nullable|file|max:3072', // max 3MB
        ]);
        $task = Task::findOrfail($taskId);

        $filePath = null;
         $remarks = e($request->comment) . "<br>";
        
        if ($request->hasFile('proof')) {
               
            $TaskActivity = new TaskAttachment();
            $TaskActivity->project_id = $task->project_id;
            $TaskActivity->task_id = $task->id;
            $file = $request->file('proof');
            $sizeInBytes = $file->getSize();

             // Optional: Convert to KB or MB        // kilobytes
            $sizeInMB = round($sizeInBytes / 1048576, 2);
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tasks'), $filename);
            $TaskActivity->file_type = $file->getClientOriginalExtension();
            $TaskActivity->file = 'uploads/tasks/' . $filename;
            $TaskActivity->name = $file->getClientOriginalName();
           
            $TaskActivity->file_size =  $sizeInMB;   
            $TaskActivity->user_id = auth()->user()->id;
            $TaskActivity->save();  // megabytes
            $remarks .= "<a class='btn btn-sm btn-success mt-2' href='{$filename}' target='_blank'>
                            {$original_name}
                            </a>";
        }
        
        $comment = new TaskComment();
        $comment->task_id = $taskId;
        $comment->project_id = $task->project_id ?? null;
        $comment->user_id = auth()->id();
        $comment->comment = $remarks;
        $comment->save();

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'comment' => e($comment->comment),
                'file_path' => $comment->file_path,
                'created_at' => $comment->created_at->format('d M, Y - h:i A'),
                'user_name' => auth()->user()->name,
                'user_avatar' => auth()->user()->avatar ?? url('images/Favicon.png'),
            ]
        ]);
    }

    public function attachment(Request $request,$id)
    
    {
        
        if ($request->hasFile('file')) {
            $task = Task::findOrfail($id);
            $TaskActivity = new TaskAttachment();
            $TaskActivity->project_id = $task->project_id;
            $TaskActivity->task_id = $task->id;
            $file = $request->file('file');
            $sizeInBytes = $file->getSize();

             // Optional: Convert to KB or MB        // kilobytes
            $sizeInMB = round($sizeInBytes / 1048576, 2);
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tasks'), $filename);
            $TaskActivity->file_type = $file->getClientOriginalExtension();
            $TaskActivity->file = 'uploads/tasks/' . $filename;
            $TaskActivity->name = $file->getClientOriginalName();
           
            $TaskActivity->file_size =  $sizeInMB;   
            $TaskActivity->user_id = auth()->user()->id;
            $TaskActivity->save();
               // megabytes
        
        }
     

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
        return back();
    }
    public function activity (Request $request,$id)
    {
        // dd($request->all());
        $task = Task::findOrfail($id);
     
        
        $att = null;
        if ($request->hasFile('proof')) {
            $TaskAttachment = new TaskAttachment();
            $TaskAttachment->project_id = $task->project_id;
            $TaskAttachment->task_id = $task->id;
            $file = $request->file('proof');
            $sizeInBytes = $file->getSize();

             // Optional: Convert to KB or MB        // kilobytes
            $sizeInMB = round($sizeInBytes / 1048576, 2);
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tasks'), $filename);
            $TaskAttachment->file_type = $file->getClientOriginalExtension();
            $TaskAttachment->file = 'uploads/tasks/' . $filename;
            $TaskAttachment->name = $file->getClientOriginalName();
           
            $TaskAttachment->file_size =  $sizeInMB;      // megabytes
            $TaskAttachment->user_id = auth()->user()->id;
            $TaskAttachment->save();
            $att = $TaskAttachment->name;
        }
        $comm = null;
        if($request->comments != null){
           
            $TaskComment = new TaskComment();
            $TaskComment->comment = $request->task." - ".$request->comments;
            $TaskComment->task_id = $id;
            $TaskComment->project_id = $task->project_id;
            $TaskComment->user_id = auth()->user()->id;
            $TaskComment->save();
            $comm = $TaskComment->comment;
        }

        $TaskActivity = new TaskActivity();
        $TaskActivity->activity = $request->task;
        $TaskActivity->task_id = $id;
        $TaskActivity->project_id = $task->project_id;
        $TaskActivity->user_id = auth()->user()->id;
        $TaskActivity->created_by = auth()->user()->id;
        $TaskActivity->hours = $request->hours;
        $TaskActivity->date = $request->date;
        $TaskActivity->file = $att;
        $TaskActivity->comments = $comm;
        $TaskActivity->save();


        $task->completed = $request->status;
        $task->save();
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
    public function updateBoard(Request $request, $id)
    {
        $request->validate([
            'project_board_id' => 'required|exists:project_boards,id'
        ]);
  
        $task = Task::findOrFail($id);
     

        $old_board = ProjectBoard::where('id',$task->project_board_id)->first();
        $new_board = ProjectBoard::where('id',$request->project_board_id)->first();
        $task->project_board_id = $request->project_board_id;
        $request->merge([
            'old_value' => $old_board->board,
            'new_value' => $new_board->board,
        ]);
        $this->createTaskComment($request,$task->project_id, $task->id, 'Update Status');
        $task->save();

        // Return the updated board name for display
        return response()->json([
            'success' => true,
            'board_name' => $task->board->board ?? 'Updated'
        ]);
    }
    public function changeMember(Request $request, $id)
    {
        // dd($request->all());
        $task = Task::findOrfail($id);
        $task->users()->sync($request->team_member);
        Alert::success('Task members updated successfully')->persistent('Dismiss');
        return back();
    }
    public function destroy($id)
    {
        $activity = TaskActivity::findOrFail($id);
        $activity->delete();

        Alert::success('Activity successfully deleted')->persistent('Dismiss');
        return back();
    }
    public function transfer(Request $request, $id)
    {
        $task = Task::findOrfail($id);
        $task->project_id = $request->project_id;
        $project_board = ProjectBoard::where('project_id', $request->project_id)->first();
        $task->project_board_id = $project_board->id; // Reset the project board
        $task->save();
        Alert::success('Task successfully transferred')->persistent('Dismiss');
        return back();
    }
    public function archive(Request $request, $id)
    {
        // Find task
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.'
            ], 404);
        }

        // Check if already completed
        if ($task->completed != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Only completed tasks can be archived.'
            ], 400);
        }

        // Update task status
        $task->archived = 1;  // Make sure you have 'archived' column in your tasks table
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task archived successfully.',
            'task_id' => $task->id
        ]);
    }

    private function createTaskComment(Request $request,$projectId, $taskId, $action)
    {
        // Build basic log: Action + Old → New
        $remarks = "<strong>{$action}</strong>: " . e($request->old_value) . 
                    " &#x2192; " . e($request->new_value) . "<br>";

        // Add optional remarks
        if (!empty($request->remarks)) {
            $remarks .= "Remarks: " . e($request->remarks) . "<br>";
        }

        $file_name = null;

        // Handle optional file upload
        if ($request->hasFile('proof')) {
            $proof = $request->file('proof');
            $original_name = $proof->getClientOriginalName();
            $name = time() . '_' . $original_name;

            // Save file
            $proof->move(public_path('proof'), $name);
            $file_path = url('proof/' . $name);

            // Append file link
            $remarks .= "<a class='btn btn-sm btn-success mt-2' href='{$file_path}' target='_blank'>
                            {$original_name}
                            </a>";

            $file_name = 'proof/' . $name;
        }

        // Save to database
        $comment = new TaskComment();
        $comment->project_id = $projectId;
        $comment->task_id = $taskId;
        $comment->user_id = auth()->id();
        $comment->comment = $remarks;
        $comment->save();

        return true;
    }
}
