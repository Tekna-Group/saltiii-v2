<?php

namespace App\Http\Controllers;
use App\Task;
use App\TaskUser;
use App\Project;
use App\TaskComment;
use App\ProjectBoard;
use App\Events\TasksSummaryGenerated;
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
    public function updateTitle(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
      
        $task = Task::findOrFail($id);

        $request->merge([
            'old_value' => $task->title,
            'new_value' => $request->title,
        ]);
        $task->title = $request->title;
        $task->save();
        
         $this->createTaskComment($request,$task->project_id, $task->id, 'Update Title');
        return response()->json([
            'success' => true,
            'message' => 'Title updated successfully',
            'task' => $task
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
    public function storeNew(Request $request)
    {
        // dd($request->all());
        // Validate the request data
       
        $projectBoard = ProjectBoard::where('project_id',$request->project)->first();
        // Create a new task instance
        $task = new Task();
        $task->project_id = $request->project;
        $task->title = $request->input('task');
        $task->description = $request->input('description');
        $task->due_date = $request->input('dueDate');
        $task->priority = $request->input('priority');
        $task->project_board_id = $projectBoard->id; // Assuming status is the ID of the project board
        $task->user_id = auth()->user()->id; // Assuming the task is created by the authenticated user
        $task->save();

      
        $projectUser = new TaskUser();
        $projectUser->task_id = $task->id;
        $projectUser->user_id = auth()->user()->id;
        $projectUser->save();
        

        // Redirect back with success message
        Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }
    public function changeStatus(Request $request)
    {
        // dd($request->all());
        $task = Task::findOrfail($request->task_id);
         $old_board = ProjectBoard::where('id',$task->project_board_id)->first();
        $new_board = ProjectBoard::where('id',$request->project_board_id)->first();
        $task->project_board_id = $request->project_board_id;
        $request->merge([
            'old_value' => $old_board->board,
            'new_value' => $new_board->board,
        ]);
        $task->completed = 0;
        if (str_contains(strtolower($new_board->board), 'complete')) {
            $task->completed = 1;
        }
        if (str_contains(strtolower($new_board->board), 'cancel')) {
            $task->completed = 1;
        }

        $this->createTaskComment($request,$task->project_id, $task->id, 'Update Status');
        $task->save();

        return response()->json(['message' => 'Task updated successfully','data' => $task,'status' => $task->completed]);
    }
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:project_boards,id',
        ]);

        // Loop through the column IDs and update their position
        foreach ($request->order as $index => $columnId) {
            ProjectBoard::where('id', $columnId)->update(['position' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Column order updated successfully',
        ]);
    }
    public function view($id)
    {
        // Fetch the task by ID

        $users = User::get();
        $projects = Project::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('completed',0)->get();
        $task = Task::with(['users', 'project', 'comments', 'attachments'])->findOrFail($id);
        $boards = ProjectBoard::where('project_id',$task->project_id)->orderBy('position','asc')->get();
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
            $remarks .= "<a class='btn btn-sm btn-success mt-2' href='{$TaskActivity->file}' target='_blank'>
                            {$TaskActivity->name}
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
                'comment' => ($comment->comment),
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

        $task->save();
        Alert::success('Successfully Encoded')->persistent('Dismiss');
        return back();
    }
    public function Newactivity (Request $request)
    {
        // dd($request->all());
        $task = Task::findOrfail($request->task_id);
     
        
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
            $TaskComment->task_id = $request->task_id;
            $TaskComment->project_id = $task->project_id;
            $TaskComment->user_id = auth()->user()->id;
            $TaskComment->save();
            $comm = $TaskComment->comment;
        }

        $TaskActivity = new TaskActivity();
        $TaskActivity->activity = $request->task;
        $TaskActivity->task_id = $request->task_id;
        $TaskActivity->project_id = $task->project_id;
        $TaskActivity->user_id = auth()->user()->id;
        $TaskActivity->created_by = auth()->user()->id;
        $TaskActivity->hours = $request->hours;
        $TaskActivity->date = $request->date;
        $TaskActivity->file = $att;
        $TaskActivity->comments = $comm;
        $TaskActivity->save();


        $task->save();
        Alert::success('Successfully Encoded')->persistent('Dismiss');
        return back();
    }
    public function storeActivityApi(Request $request, $id)
    {
    $task = Task::findOrFail($id);

    $attachmentName = null;
    if ($request->hasFile('proof')) {
        $file = $request->file('proof');
        $sizeInMB = round($file->getSize() / 1048576, 2); // MB
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/tasks'), $filename);

        $taskAttachment = new TaskAttachment();
        $taskAttachment->project_id = $task->project_id;
        $taskAttachment->task_id = $task->id;
        $taskAttachment->file_type = $file->getClientOriginalExtension();
        $taskAttachment->file = 'uploads/tasks/' . $filename;
        $taskAttachment->name = $file->getClientOriginalName();
        $taskAttachment->file_size = $sizeInMB;
        $taskAttachment->user_id = auth()->id();
        $taskAttachment->save();

        $attachmentName = $taskAttachment->name;
    }

    $commentText = null;
    if (!empty($request->comments)) {
        $taskComment = new TaskComment();
        $taskComment->task_id = $task->id;
        $taskComment->project_id = $task->project_id;
        $taskComment->user_id = auth()->id();
        $taskComment->comment = $request->task . " - " . $request->comments;
        $taskComment->save();

        $commentText = $taskComment->comment;
    }

    $taskActivity = new TaskActivity();
    $taskActivity->task_id = $task->id;
    $taskActivity->project_id = $task->project_id;
    $taskActivity->user_id = auth()->id();
    $taskActivity->created_by = auth()->id();
    $taskActivity->activity = $request->task;
    $taskActivity->hours = $request->hours;
    $taskActivity->date = $request->date;
    $taskActivity->file = $attachmentName;
    $taskActivity->comments = $commentText;
    $taskActivity->save();

    $task->save();

    return response()->json([
        'success'    => true,
        'activity'   => [
            'id'         => $taskActivity->id,
            'task'       => $taskActivity->activity,
            'hours'      => $taskActivity->hours,
            'date'       => date('d M, Y',strtotime($taskActivity->date)),
            'date_old'       => $taskActivity->date,
            'user_name'  => auth()->user()->name,
            'user_avatar'=> auth()->user()->avatar,
            'comments'   => $commentText,
            'file_name'  => $attachmentName,
        ],
        'message'    => 'Activity created successfully',
    ]);
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

        $old_board = ProjectBoard::where('id',$task->project_board_id)->first();
        $task->project_board_id = $request->project_board_id;
       
        $new_board = ProjectBoard::where('id',$request->project_board_id)->first();
        $task->project_board_id = $request->project_board_id;
        $request->merge([
            'old_value' => $old_board->board,
            'new_value' => $new_board->board,
        ]);
       if (str_contains(strtolower($new_board->board), 'complete')) {
            $task->completed = 1;
        }
        else
        {
             $task->completed = 0;
        }
        $this->createTaskComment($request,$task->project_id, $task->id, 'Update Status');
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
        $task->completed = 0;
       if (str_contains(strtolower($new_board->board), 'complete')) {
            $task->completed = 1;
        }
        if (str_contains(strtolower($new_board->board), 'cancel')) {
            $task->completed = 1;
        }
        $this->createTaskComment($request,$task->project_id, $task->id, 'Update Status');
        $task->save();

        // Return the updated board name for display
        return response()->json([
            'success' => true,
            'board_name' => $task->board->board ?? 'Updated',
            'status' => $task->completed,
        ]);
    }
    public function changeMember(Request $request, $id)
    {
        // dd($request->all());
        $task = Task::findOrfail($id);
        $task->users()->sync($request->team_member);
        Alert::success('Transferred Successfully')->persistent('Dismiss');
        return back();
    }
    public function destroy(Request $request,$id)
    {
        $activity = TaskActivity::find($id);

        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Activity not found']);
        }
        $request->merge([
            'old_value' => $activity->activity." - ".$activity->hours." Hours",
            'new_value' => "Deleted",
        ]);
        $this->createTaskComment($request,$activity->project_id, $activity->task_id, 'Delete Activity');
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
        // if ($task->completed != 1) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Only completed tasks can be archived.'
        //     ], 400);
        // }

        // Update task status
        $task->archived = 1;  // Make sure you have 'archived' column in your tasks table
        $task->completed = 1;  // Make sure you have 'archived' column in your tasks table
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
    public function destroyApi(Request $request,$id)
    {
        $activity = TaskActivity::find($id);

        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Activity not found']);
        }
        $request->merge([
            'old_value' => $activity->activity." - ".$activity->hours." Hours",
            'new_value' => "Deleted",
        ]);
        $this->createTaskComment($request,$activity->project_id, $activity->task_id, 'Delete Activity');
        $activity->delete();

        return response()->json(['success' => true, 'message' => 'Activity deleted successfully']);
    }
    public function updateApi(Request $request, $id)
    {
        $activity = TaskActivity::findOrFail($id);
        $request->merge([
            'old_value' => $activity->activity." - ".$activity->hours." Hours",
            'new_value' => $request->task." - ".$request->hours." Hours",
        ]);
        $this->createTaskComment($request,$activity->project_id, $activity->task_id, 'Update Activity');
        $activity->activity = $request->task;
        $activity->hours = $request->hours;
        $activity->save();

        return response()->json([
            'success' => true,
            'activity' => [
                'task' => $activity->activity,
                'hours' => $activity->hours,
                'date' => $activity->date
            ]
        ]);
    }
    public function getByDate($date)
    {
        $activities = TaskActivity::with('project')
            ->where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->get()
            ->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'activity' => $activity->activity,
                    'hours' => $activity->hours,
                    'project_name' => $activity->project->name ?? null
                ];
            }); 

        return response()->json(['activities' => $activities]);
    }

    public function updateActivityAPI(Request $request, $id)
    {
        $last_sunday = date('Y-m-d',strtotime('last sunday'));
        $saturday = date("Y-m-d", strtotime("+6 days",strtotime($last_sunday)));
      
        $activity = TaskActivity::find($id);
        
        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Activity not found.']);
        }
    
        // Validation
        $request->validate([
            'activity' => 'required|string|max:255',
            'hours' => 'required|numeric|min:0.1'
        ]);
    
        // Update activity
        $request->merge([
            'old_value' => $activity->activity." - ".$activity->hours." Hours",
            'new_value' => $request->task." - ".$request->hours." Hours",
        ]);
        $this->createTaskComment($request,$activity->project_id, $activity->task_id, 'Update Activity');
        $activity->activity = $request->activity;
        $activity->hours = $request->hours;
        $activity->save();
        $total_hours = TaskActivity::where('user_id', auth()->id())
        ->whereBetween('date', [$last_sunday, $saturday])
        ->sum('hours');
        return response()->json([
            'success' => true,
            'hours' => number_format($total_hours,2),
            'date' => $activity->date, // e.g. "2025-09-11"
            'activities' => TaskActivity::where('user_id', auth()->id())
                                    ->where('date', $activity->date)
                                    ->get(['id', 'activity', 'hours'])
        ]);
    }

    public function destroyActivityAPI($id)
    {
        $last_sunday = date('Y-m-d',strtotime('last sunday'));
        $saturday = date("Y-m-d", strtotime("+6 days",strtotime($last_sunday)));
      
        $activity = TaskActivity::findOrFail($id);
        $activity->delete();
        $total_hours = TaskActivity::where('user_id', auth()->id())
        ->whereBetween('date', [$last_sunday, $saturday])
        ->sum('hours');
        return response()->json(['success' => true,
        'hours' => number_format($total_hours,2),
        ]);
    }
    public function sendDailyTaskSummary()
    {
        $user = User::find(1);

        $tasks = [
            'delayed' => $user->tasks()
                ->where('due_date', '<', date('Y-m-d'))
                ->where('completed',0)
                ->orderBy('due_date','asc')
                ->get(),

            'due_today' => $user->tasks()
                ->whereDate('due_date', date('Y-m-d'))
                 ->where('completed',0)
                 ->orderBy('due_date','asc')
                ->get(),

            'upcoming' => $user->tasks()
                ->where('due_date', '>', date('Y-m-d'))
                 ->where('completed',0)
                 ->orderBy('due_date','asc')
                ->get(),
        ];
     
        event(new TasksSummaryGenerated($user, $tasks));
    }

}
