<?php

namespace App\Http\Controllers;
use App\Project;
use App\User;
use App\ProjectUser;
use App\ProjectBoard;
use Illuminate\Http\Request;

use RealRashid\SweetAlert\Facades\Alert;

class ProjectController extends Controller
{
    //

    public function index()
    {
        // Fetch all projects from the database
        // $projects = \App\Models\Project::all();
        $projects = Project::with(['parent', 'children.tasks', 'tasks'])
        ->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy('name','asc')->where('completed','!=',1)->get();
        $users = User::assignableFor(auth()->user());
        // Return the view with the projects data
        return view('projects.index',
            array(
                'projects' => $projects,
                'users' => $users,
            )
        );
    }
    public function store(Request $request)
    {
        // Validate the request data
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:projects,id',
        ]);

        if ($request->filled('parent_id')) {
            $parentProjectQuery = Project::query();

            if (auth()->user()->role !== 'Admin') {
                $parentProjectQuery->whereHas('users', function ($query) {
                    $query->where('user_id', auth()->id());
                });
            }

            $parentProjectQuery->findOrFail($request->parent_id);
        }

        $assignableUserIds = User::assignableFor(auth()->user())->pluck('id')->toArray();
        $requestedMembers = collect($request->input('team_member', []))->map(function ($id) {
            return (int) $id;
        })->unique()->values();

        if ($requestedMembers->diff($assignableUserIds)->isNotEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'One or more selected members are not part of your team group.',
                    'errors' => [
                        'team_member' => ['One or more selected members are not part of your team group.'],
                    ],
                ], 422);
            }

            return back()->withErrors(['team_member' => 'One or more selected members are not part of your team group.']);
        }

        // Create a new project instance
        $project = new Project();
        $project->parent_id = $request->input('parent_id');
        $project->name = $request->input('name');
        $project->description = $request->input('description');
        $project->status = $request->input('status');
        $project->user_id = auth()->user()->id; // Assuming the project is created by the authenticated user
        $project->save();
        // Handle file upload for project icon
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/projects'), $filename);
            $project->icon = 'uploads/projects/' . $filename;
            $project->save();
        } 
        // Attach team members to the project
        foreach($requestedMembers as $memberId) {
            $projectUser = new ProjectUser();
            $projectUser->project_id = $project->id;
            $projectUser->user_id = $memberId;
            $projectUser->save();
        }

        // Redirect back with success message
        foreach (['To do', 'Ongoing', 'For Review', 'Completed','On Hold','Cancelled','Recurring Task'] as $boardName) {
            
            $projectBoard = new ProjectBoard;
            $projectBoard->project_id = $project->id;
            $projectBoard->board = $boardName;
            $projectBoard->save();
        }
       

        if ($request->ajax() || $request->wantsJson()) {
            $project->load('users');

            return response()->json([
                'success' => true,
                'message' => 'Sub-project created successfully.',
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'url' => url('/view-project/'.$project->id),
                    'progress' => 0,
                    'tasks' => 0,
                    'open_tasks' => 0,
                    'hours' => 0,
                    'users' => $project->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'avatar' => asset($user->avatar ?: 'images/Favicon.png'),
                        ];
                    })->values(),
                ],
            ]);
        }

        Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
        
    }

    public function view(Request $request,$id)
    {

       $project = Project::with([
            'parent',
            'children' => function ($query) {
                $query->where('completed', '!=', 1)->orderBy('name', 'asc');
            },
            'children.tasks.comments',
            'children.tasks.attachments',
            'children.tasks.activities',
            'children.tasks.users',
            'children.users',
            'users',
            // Sort statuses by position ASC when eager loading
            'statuses' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'tasks',
            'tasks.comments',
            'tasks.attachments',
            'tasks.activities', // Prevent N+1
            'tasks.users'       // Prevent N+1
        ])->findOrFail($id);
        $boardData = [];
        
        foreach ($project->statuses as $status) {
            $tasks = $project->tasks->where('archived', '!=',1)
        ->where('project_board_id', $status->id)
        ->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->title,
                'description' => $task->description,
                'due_date' => $task->due_date ? $task->due_date : null,
                'priority' => $task->priority,
                'comments' => $task->comments->count(),
                'attachments' => $task->attachments->count(),
                'hours' => $task->activities->sum('hours'),
                'completed' => $task->completed,
                'users' => $task->users,
                'assignees' => $task->users->pluck('name')->toArray(),
            ];
        })
        ->sortBy(function ($task) {
            return [
                $task['completed'],           // 0 first, then 1
                $task['due_date'] ?? '9999-12-31', // Nulls go last
            ];
        })
        ->values(); // Re-index the collection
            
                $boardData[] = [
                    'id' => $status->id, // e.g. "To Do" -> "todo"
                    'name' => $status->board,
                    'tasks' => $tasks
                ];
            }
            // Return the view with the projects data
        
            $users = User::assignableFor(auth()->user());
            $projects = Project::with('parent')
                ->when(auth()->user()->role !== 'Admin', function ($query) {
                    $query->whereHas('users', function ($query) {
                        $query->where('user_id', auth()->id());
                    });
                })
                ->where('completed', '!=', 1)
                ->orderBy('name', 'asc')
                ->get();
            return view('projects.view',
                array(
                    'project' => $project,
                    'projects' => $projects,
                    'users' => $users,
                    'boardData' => $boardData,
                )
            );
    }
     public function viewPublic(Request $request,$id)
    {

       $project = Project::with([
            'parent',
            'children' => function ($query) {
                $query->where('completed', '!=', 1)->orderBy('name', 'asc');
            },
            'children.tasks',
            'children.users',
            'users',
            // Sort statuses by position ASC when eager loading
            'statuses' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'tasks',
            'tasks.comments',
            'tasks.attachments',
            'tasks.activities', // Prevent N+1
            'tasks.users'       // Prevent N+1
        ])->findOrFail($id);
        $boardData = [];
        
        foreach ($project->statuses as $status) {
            $tasks = $project->tasks->where('archived', '!=',1)
        ->where('project_board_id', $status->id)
        ->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->title,
                'description' => $task->description,
                'due_date' => $task->due_date ? $task->due_date : null,
                'priority' => $task->priority,
                'comments' => $task->comments->count(),
                'attachments' => $task->attachments->count(),
                'hours' => $task->activities->sum('hours'),
                'completed' => $task->completed,
                'users' => $task->users,
                'assignees' => $task->users->pluck('name')->toArray(),
            ];
        })
        ->sortBy(function ($task) {
            return [
                $task['completed'],           // 0 first, then 1
                $task['due_date'] ?? '9999-12-31', // Nulls go last
            ];
        })
        ->values(); // Re-index the collection
            
                $boardData[] = [
                    'id' => $status->id, // e.g. "To Do" -> "todo"
                    'name' => $status->board,
                    'tasks' => $tasks
                ];
            }
            // Return the view with the projects data
        
            $users = User::get();
            return view('projects.view-public',
                array(
                    'project' => $project,
                    'users' => $users,
                    'boardData' => $boardData,
                )
            );
    }
    public function teamMember(Request $request,$id)
    {
        $assignableUserIds = User::assignableFor(auth()->user())->pluck('id')->toArray();
        $requestedMembers = collect($request->input('team_member', []))->map(function ($id) {
            return (int) $id;
        })->unique()->values();

        if ($requestedMembers->diff($assignableUserIds)->isNotEmpty()) {
            return back()->withErrors(['team_member' => 'One or more selected members are not part of your team group.']);
        }

        ProjectUser::where('project_id',$id)->delete();

          foreach($requestedMembers as $memberId) {
            $projectUser = new ProjectUser();
            $projectUser->project_id = $id;
            $projectUser->user_id = $memberId;
            $projectUser->save();
        }

            // Redirect back with success message
        Alert::success('Successfully Updated')->persistent('Dismiss');
        return back();
    }

    public function boardProject(Request $request,$id)
    {
        $project = new ProjectBoard;
        $project->project_id = $id;
        $project->board = $request->boardName;
        $project->save();

         Alert::success('Successfully Encoded')->persistent('Dismiss');
        return back();
    }

    public function editBoard(Request $request)
    {
        $id = $request->input('statusId');
        $name = $request->input('statusName');

        if (!$id || !$name) {
            return back()->withErrors(['error' => 'Status ID and name are required']);
        }

        $status = ProjectBoard::find($id);
        if (!$status) {
            return back()->withErrors(['error' => 'Status not found']);
        }

        $status->board = $name;
        $status->save();
        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
    public function markComplete($id)
    {
        $project = Project::findOrFail($id);
        $project->completed = 1;
        $project->status = 'Completed';
        $project->save();
        Alert::success('Project marked as completed.')->persistent('Dismiss');
        return back();
    }
    public function delete($id)
    {
        $project = Project::findOrFail($id);
        $project->completed = 1;
        $project->status = 'Archived';
        $project->save();
        Alert::success('Project marked as archived.')->persistent('Dismiss');
        return back();
    }
    public function updateTitle(Request $request,$id)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        ]);

        $project = Project::findOrFail($id);
        $project->name = $request->name;
        $project->save();

        return response()->json([
            'success' => true,
            'name' => $project->name
        ]);
    }
    public function destroy($id)
    {
          if (!auth()->user()->role == 'Admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $status = ProjectBoard::find($id);

        if (!$status) {
            return response()->json(['success' => false, 'message' => 'Status not found.'], 404);
        }

        $status->delete();

        return response()->json(['success' => true, 'message' => 'Status deleted successfully.']);
    }

}
