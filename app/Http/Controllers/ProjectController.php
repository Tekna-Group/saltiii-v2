<?php

namespace App\Http\Controllers;
use App\Project;
use App\User;
use App\ProjectUser;
use App\ProjectBoard;
use App\Task;
use App\TaskActivity;
use App\TaskUser;
use App\Services\TaskTrackerImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function importTasks(Request $request, $id, TaskTrackerImport $importer)
    {
        $request->validate([
            'import_file' => 'required|file|max:5120',
            'assignee_id' => 'required|integer',
        ]);

        $project = Project::when(auth()->user()->role !== 'Admin', function ($query) {
            $query->whereHas('users', function ($userQuery) {
                $userQuery->where('users.id', auth()->id());
            });
        })->findOrFail($id);

        $assignableUsers = User::assignableFor(auth()->user());
        $assignee = $assignableUsers->firstWhere('id', (int) $request->input('assignee_id'));
        if (!$assignee) {
            return back()->withErrors([
                'assignee_id' => 'Select a person in charge from your team.',
            ])->withInput();
        }

        $file = $request->file('import_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'csv'], true)) {
            return back()->withErrors([
                'import_file' => 'Use an .xlsx or .csv tracker file.',
            ])->withInput();
        }

        try {
            $rows = $importer->read($file->getRealPath(), $extension);
        } catch (\RuntimeException $exception) {
            return back()->withErrors([
                'import_file' => $exception->getMessage(),
            ])->withInput();
        }

        $boards = $project->statuses()->orderBy('position', 'asc')->get();
        if ($boards->isEmpty()) {
            return back()->withErrors([
                'import_file' => 'Create at least one project status before importing tasks.',
            ])->withInput();
        }

        $defaultBoard = $boards->first(function ($board) {
            return in_array($this->normalizeImportValue($board->board), ['todo', 'tobedone', 'open'], true);
        }) ?: $boards->first();

        $existingTitles = Task::where('project_id', $project->id)
            ->pluck('title')
            ->mapWithKeys(function ($title) {
                return [$this->normalizeImportTitle($title) => true];
            })
            ->all();

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $rows,
            $project,
            $boards,
            $defaultBoard,
            $assignee,
            &$existingTitles,
            &$created,
            &$skipped
        ) {
            foreach ($rows as $row) {
                $title = $this->buildImportTitle($row);
                $titleKey = $this->normalizeImportTitle($title);

                if (isset($existingTitles[$titleKey])) {
                    $skipped++;
                    continue;
                }

                $board = $this->resolveImportBoard($row['status'], $boards, $defaultBoard);
                $task = new Task();
                $task->project_id = $project->id;
                $task->project_board_id = $board->id;
                $task->title = $title;
                $task->description = $this->buildImportDescription($row);
                $task->priority = $this->normalizeImportPriority($row['priority']);
                $task->completed = $this->isCompletedImportStatus($row['status'], $board->board) ? 1 : 0;
                $task->archived = 0;
                $task->user_id = auth()->id();
                $task->save();

                $taskUser = new TaskUser();
                $taskUser->task_id = $task->id;
                $taskUser->user_id = $assignee->id;
                $taskUser->save();

                $existingTitles[$titleKey] = true;
                $created++;
            }
        });

        Alert::success(
            'Tracker imported',
            $created.' tasks assigned to '.$assignee->name.'. '.$skipped.' duplicates skipped.'
        )->persistent('Dismiss');

        return back();
    }

    private function boardTaskQuery($projectId, $boardId, $withAssignees = true)
    {
        $query = Task::query()
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
            }, 'hours_total');

        if ($withAssignees) {
            $query->with(['users' => function ($query) {
                $query->select('users.id', 'users.name');
            }]);
        }

        return $query->orderBy('tasks.completed', 'asc')
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

    private function buildImportTitle(array $row)
    {
        $parts = array_values(array_filter([
            $row['module'],
            $row['screen_feature'],
        ], function ($value) {
            return trim((string) $value) !== '';
        }));

        $subject = $parts ? implode(' - ', $parts) : $row['description'];
        $subject = trim((string) $subject) !== '' ? $subject : 'Imported tracker task';
        $prefix = $row['bug_cr'] !== '' ? '['.$row['bug_cr'].'] ' : '';

        $title = trim($prefix.$subject);

        return function_exists('mb_substr') ? mb_substr($title, 0, 255, 'UTF-8') : substr($title, 0, 255);
    }

    private function buildImportDescription(array $row)
    {
        $details = [
            'Type' => $row['type'],
            'Reported by' => $row['reported_by'],
            'Date reported' => $row['date_reported'],
            'Source status' => $row['status'],
        ];

        $html = '<p>'.nl2br(e($row['description'])).'</p>';
        $html .= '<p><strong>Tracker details</strong><br>';
        foreach ($details as $label => $value) {
            if ($value !== '') {
                $html .= '<strong>'.e($label).':</strong> '.e($value).'<br>';
            }
        }
        $html .= '</p>';

        if ($row['notes_dev_action'] !== '') {
            $html .= '<p><strong>Notes / Dev Action</strong><br>'.nl2br(e($row['notes_dev_action'])).'</p>';
        }

        return $html;
    }

    private function normalizeImportPriority($priority)
    {
        $value = $this->normalizeImportValue($priority);
        if ($value === 'high' || $value === 'urgent' || $value === 'critical') {
            return 'High';
        }
        if ($value === 'low') {
            return 'Low';
        }

        return 'Medium';
    }

    private function resolveImportBoard($status, $boards, ProjectBoard $defaultBoard)
    {
        $value = $this->normalizeImportValue($status);
        $aliases = [
            'done' => ['completed', 'complete', 'done'],
            'closed' => ['completed', 'complete', 'done'],
            'complete' => ['completed', 'complete', 'done'],
            'completed' => ['completed', 'complete', 'done'],
            'new' => ['todo', 'tobedone', 'open'],
            'pending' => ['todo', 'tobedone', 'open'],
            'open' => ['todo', 'tobedone', 'open'],
            'todo' => ['todo', 'tobedone', 'open'],
            'inprogress' => ['ongoing', 'inprogress', 'doing', 'working'],
            'ongoing' => ['ongoing', 'inprogress', 'doing', 'working'],
            'review' => ['forreview', 'review', 'qa'],
            'forreview' => ['forreview', 'review', 'qa'],
            'hold' => ['onhold', 'hold', 'blocked'],
            'onhold' => ['onhold', 'hold', 'blocked'],
            'cancelled' => ['cancelled', 'canceled'],
            'canceled' => ['cancelled', 'canceled'],
            'recurring' => ['recurring', 'recurringtask'],
            'recurringtask' => ['recurring', 'recurringtask'],
        ];
        $targets = isset($aliases[$value]) ? $aliases[$value] : [$value];

        $match = $boards->first(function ($board) use ($targets) {
            return in_array($this->normalizeImportValue($board->board), $targets, true);
        });

        return $match ?: $defaultBoard;
    }

    private function normalizeImportTitle($title)
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower(trim((string) $title), 'UTF-8')
            : strtolower(trim((string) $title));
    }

    private function isCompletedImportStatus($sourceStatus, $boardName)
    {
        $completedValues = ['done', 'closed', 'complete', 'completed'];

        return in_array($this->normalizeImportValue($sourceStatus), $completedValues, true)
            || in_array($this->normalizeImportValue($boardName), $completedValues, true);
    }

    private function normalizeImportValue($value)
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', trim((string) $value)));
    }
    public function createPublicShare($id)
    {
        abort_unless(auth()->user()->role === 'Admin', 403);

        $project = Project::findOrFail($id);
        if (!$project->public_share_token) {
            do {
                $project->public_share_token = Str::random(64);
            } while (Project::where('public_share_token', $project->public_share_token)->exists());
        }

        $project->public_share_enabled_at = now();
        $project->save();

        Alert::success('Public link created', 'Anyone with the link can view the project board.')->persistent('Dismiss');

        return back()->with('open_public_share', true);
    }

    public function revokePublicShare($id)
    {
        abort_unless(auth()->user()->role === 'Admin', 403);

        $project = Project::findOrFail($id);
        $project->public_share_token = null;
        $project->public_share_enabled_at = null;
        $project->save();

        Alert::success('Public link disabled', 'The previous shared link can no longer be opened.')->persistent('Dismiss');

        return back();
    }

    public function viewPublic(Request $request, $token)
    {
        $project = Project::with(['statuses' => function ($query) {
            $query->orderBy('position', 'asc');
        }])
            ->where('public_share_token', $token)
            ->whereNotNull('public_share_enabled_at')
            ->firstOrFail();

        $activeTasks = Task::where('project_id', $project->id)
            ->where(function ($query) {
                $query->where('archived', '!=', 1)->orWhereNull('archived');
            });
        $boardTotals = (clone $activeTasks)
            ->groupBy('project_board_id')
            ->select('project_board_id')
            ->selectRaw('COUNT(*) as task_count')
            ->pluck('task_count', 'project_board_id');
        $boardData = [];

        foreach ($project->statuses as $status) {
            $total = (int) $boardTotals->get($status->id, 0);
            $tasks = $this->boardTaskQuery($project->id, $status->id, false)
                ->limit(10)
                ->get()
                ->map(function ($task) {
                    return $this->formatPublicBoardTask($task);
                })
                ->values();

            $boardData[] = [
                'id' => $status->id,
                'name' => $status->board,
                'total' => $total,
                'tasks' => $tasks,
            ];
        }

        return response()->view('projects.view-public', [
            'project' => $project,
            'boardData' => $boardData,
        ])->withHeaders([
            'Cache-Control' => 'private, no-store, max-age=0',
            'Referrer-Policy' => 'no-referrer',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }

    public function publicBoardTasks($token, $boardId)
    {
        $project = Project::where('public_share_token', $token)
            ->whereNotNull('public_share_enabled_at')
            ->firstOrFail();
        $board = $project->statuses()->where('id', $boardId)->firstOrFail();
        $tasks = $this->boardTaskQuery($project->id, $board->id, false)
            ->get()
            ->map(function ($task) {
                return $this->formatPublicBoardTask($task);
            })
            ->values();

        return response()->json([
            'tasks' => $tasks,
            'total' => $tasks->count(),
        ])->withHeaders([
            'Cache-Control' => 'private, no-store, max-age=0',
            'Referrer-Policy' => 'no-referrer',
        ]);
    }

    private function formatPublicBoardTask(Task $task)
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
        ];
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
        $completedTaskCount = 0;

        DB::transaction(function () use ($project, &$completedTaskCount) {
            $completedTaskCount = Task::where('project_id', $project->id)
                ->where(function ($query) {
                    $query->where('completed', '!=', 1)->orWhereNull('completed');
                })
                ->update(['completed' => 1]);

            $project->completed = 1;
            $project->status = 'Completed';
            $project->save();
        });

        Alert::success(
            'Project completed',
            $completedTaskCount.' unfinished '.str_plural('task', $completedTaskCount).' marked as completed.'
        )->persistent('Dismiss');
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
