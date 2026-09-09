<?php

namespace App\Http\Controllers;
use App\Project;
use App\User;
use App\ProjectUser;
use App\ProjectBoard;
use App\Task;
use App\TaskActivity;
use Illuminate\Http\Request;

use RealRashid\SweetAlert\Facades\Alert;

class ProjectController extends Controller
{
    //

    public function index()
    {
        // Fetch all projects from the database
        // $projects = \App\Models\Project::all();
        $projects = Project::with(['parent', 'children.tasks', 'tasks', 'users'])
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
                $query->where('completed', '!=', 1)
                    ->withCount([
                        'tasks as active_tasks_count' => function ($taskQuery) {
                            $taskQuery->where(function ($activeQuery) {
                                $activeQuery->where('archived', '!=', 1)->orWhereNull('archived');
                            });
                        },
                        'tasks as completed_tasks_count' => function ($taskQuery) {
                            $taskQuery->where(function ($activeQuery) {
                                $activeQuery->where('archived', '!=', 1)->orWhereNull('archived');
                            })->where('completed', 1);
                        },
                    ])
                    ->orderBy('name', 'asc');
            },
            'children.users',
            'users',
            'statuses' => function ($query) {
                $query->orderBy('position', 'asc');
            },
        ])->when(auth()->user()->role !== 'Admin', function ($query) {
            $query->whereHas('users', function ($userQuery) {
                $userQuery->where('users.id', auth()->id());
            });
        })->findOrFail($id);

        $activeTasks = Task::where('project_id', $project->id)
            ->where(function ($query) {
                $query->where('archived', '!=', 1)->orWhereNull('archived');
            });
        $taskStats = (clone $activeTasks)
            ->selectRaw('COUNT(*) as total_tasks')
            ->selectRaw('COALESCE(SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END), 0) as completed_tasks')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN (completed IS NULL OR completed != 1) AND due_date IS NOT NULL AND due_date < ? THEN 1 ELSE 0 END), 0) as overdue_tasks',
                [date('Y-m-d')]
            )
            ->first();
        $projectTaskCount = (int) $taskStats->total_tasks;
        $projectCompletedCount = (int) $taskStats->completed_tasks;
        $projectOverdueCount = (int) $taskStats->overdue_tasks;
        $projectHours = TaskActivity::whereIn('task_id', (clone $activeTasks)->select('id'))->sum('hours');
        $projectOpenCount = max(0, $projectTaskCount - $projectCompletedCount);
        $projectProgress = $projectTaskCount > 0
            ? round(($projectCompletedCount / $projectTaskCount) * 100)
            : 0;

        $childIds = $project->children->pluck('id');
        $childHours = collect();
        if ($childIds->isNotEmpty()) {
            $childHours = TaskActivity::join('tasks', 'tasks.id', '=', 'task_activities.task_id')
                ->whereIn('tasks.project_id', $childIds)
                ->where(function ($query) {
                    $query->where('tasks.archived', '!=', 1)->orWhereNull('tasks.archived');
                })
                ->groupBy('tasks.project_id')
                ->select('tasks.project_id')
                ->selectRaw('COALESCE(SUM(task_activities.hours), 0) as total_hours')
                ->pluck('total_hours', 'project_id');
        }
        $project->children->each(function ($child) use ($childHours) {
            $child->setAttribute('total_hours', (float) $childHours->get($child->id, 0));
        });

        $boardData = [];
        $boardTotals = (clone $activeTasks)
            ->groupBy('project_board_id')
            ->select('project_board_id')
            ->selectRaw('COUNT(*) as task_count')
            ->pluck('task_count', 'project_board_id');

        foreach ($project->statuses as $status) {
            $total = (int) $boardTotals->get($status->id, 0);
            $tasks = $this->boardTaskQuery($project->id, $status->id)->limit(10)->get()->map(function ($task) {
                return $this->formatBoardTask($task);
            })->values();

            $boardData[] = [
                'id' => $status->id,
                'name' => $status->board,
                'total' => $total,
                'loaded' => $total <= 10,
                'tasks' => $tasks,
            ];
        }

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

        return view('projects.view', [
            'project' => $project,
            'projects' => $projects,
            'users' => $users,
            'boardData' => $boardData,
            'projectTaskCount' => $projectTaskCount,
            'projectCompletedCount' => $projectCompletedCount,
            'projectOpenCount' => $projectOpenCount,
            'projectOverdueCount' => $projectOverdueCount,
            'projectHours' => $projectHours,
            'projectProgress' => $projectProgress,
        ]);
    }

    public function boardTasks(Request $request, $projectId, $boardId)
    {
        $project = Project::when(auth()->user()->role !== 'Admin', function ($query) {
            $query->whereHas('users', function ($userQuery) {
                $userQuery->where('users.id', auth()->id());
            });
        })->findOrFail($projectId);

        $board = $project->statuses()->where('id', $boardId)->firstOrFail();
        $tasks = $this->boardTaskQuery($project->id, $board->id)->get()->map(function ($task) {
            return $this->formatBoardTask($task);
        })->values();

        return response()->json([
            'tasks' => $tasks,
            'total' => $tasks->count(),
        ]);
    }

    private function boardTaskQuery($projectId, $boardId)
    {
        return Task::query()
            ->select([
                'tasks.id',
                'tasks.title',
                'tasks.due_date',
                'tasks.priority',
                'tasks.completed',
                'tasks.project_board_id',
            ])
            ->where('tasks.project_id', $projectId)
            ->where('tasks.project_board_id', $boardId)
            ->where(function ($query) {
                $query->where('tasks.archived', '!=', 1)->orWhereNull('tasks.archived');
            })
            ->withCount(['comments', 'attachments'])
            ->selectSub(function ($query) {
                $query->from('task_activities')
                    ->selectRaw('COALESCE(SUM(task_activities.hours), 0)')
                    ->whereColumn('task_activities.task_id', 'tasks.id');
            }, 'hours_total')
            ->with(['users' => function ($query) {
                $query->select('users.id', 'users.name');
            }])
            ->orderBy('tasks.completed', 'asc')
            ->orderByRaw('CASE WHEN tasks.due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('tasks.due_date', 'asc')
            ->orderBy('tasks.id', 'asc');
    }

    private function formatBoardTask(Task $task)
    {
        return [
            'id' => $task->id,
            'name' => $task->title,
            'due_date' => $task->due_date ?: null,
            'priority' => $task->priority,
            'comments' => (int) $task->comments_count,
            'attachments' => (int) $task->attachments_count,
            'hours' => (float) $task->hours_total,
            'completed' => (int) $task->completed,
            'assignees' => $task->users->pluck('name')->values()->all(),
        ];
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
                'due_date' => $task->due_date ? $task->due_date : null,
                'priority' => $task->priority,
                'comments' => $task->comments->count(),
                'attachments' => $task->attachments->count(),
                'hours' => $task->activities->sum('hours'),
                'completed' => $task->completed,
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
