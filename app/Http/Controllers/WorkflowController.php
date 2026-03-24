<?php

namespace App\Http\Controllers;
use App\Project;
use App\User;
use App\ProjectBoard;
use App\WorkflowBoard;
use App\WorkflowBoardTransition;
use App\WorkflowBoardAssignee;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    //
    public function index()
    {
        $projects = Project::all();
        return view('workflow.index', compact('projects'));
    }

        public function boards($projectId)
        {
            // Get all boards for the project
            $boards = ProjectBoard::where('project_id', $projectId)
                ->orderBy('order')
                ->get();

            // Get allowed transitions
            $transitions = WorkflowBoardTransition::where('project_id', $projectId)
                ->where('is_allowed', true)
                ->get();

            // Get all users for this project (to populate select)
            $users = User::whereHas('projects', function($q) use ($projectId) {
                $q->where('projects.id', $projectId);
            })->orderBy('name')->get();

            // Get assigned users per board
            $assignedUsers = WorkflowBoardAssignee::where('project_id', $projectId)
                ->get()
                ->groupBy('board_id'); 
                // Grouping by board_id makes it easy to fetch for each board

            return response()->json([
                'boards' => $boards,
                'transitions' => $transitions,
                'users' => $users,
                'assignedUsers' => $assignedUsers
            ]);

}

    public function configure($boardId)
{
    
    $board = ProjectBoard::findOrFail($boardId);

    $boards = ProjectBoard::where('project_id', $board->project_id)
        ->orderBy('order')
        ->get();

    // Auto-create transitions if none exist
    foreach ($boards as $from) {
        foreach ($boards as $to) {
            WorkflowBoardTransition::firstOrCreate([
                'project_id' => $board->project_id,
                'from_board_id' => $from->id,
                'to_board_id' => $to->id,
            ], [
                'is_allowed' => ($from->id !== $to->id)
            ]);
        }
    }

    $transitions = WorkflowBoardTransition::where('project_id', $board->project_id)->get();

    return view('workflow.configure', compact('boards', 'transitions', 'board'));
}

    public function saveTransition(Request $request)
    {
        WorkflowBoardTransition::updateOrCreate([
            'project_id' => $request->project_id,
            'from_board_id' => $request->from_board_id,
            'to_board_id' => $request->to_board_id,
        ], [
            'is_allowed' => $request->has('is_allowed')
        ]);

        return back();
    }
    public function saveAssignees(Request $request)
{
    WorkflowBoardAssignee::where([
        'project_id' => $request->project_id,
        'board_id' => $request->board_id
    ])->delete();

    if ($request->user_ids) {
        foreach ($request->user_ids as $userId) {
            WorkflowBoardAssignee::create([
                'project_id' => $request->project_id,
                'board_id' => $request->board_id,
                'user_id' => $userId,
                'fallback_rule' => $request->fallback_rule,
                'replace_existing' => true
            ]);
        }
    } else {
        // Save fallback rule even without users
        WorkflowBoardAssignee::create([
            'project_id' => $request->project_id,
            'board_id' => $request->board_id,
            'fallback_rule' => $request->fallback_rule
        ]);
    }

    return back()->with('success','Assignment saved');
}
public function applyAutoAssignment(Task $task, $movedByUserId)
{
    $configs = WorkflowBoardAssignee::where([
        'project_id' => $task->project_id,
        'board_id' => $task->project_board_id
    ])->get();

    // CASE 1: Board has assigned users
    if ($configs->whereNotNull('user_id')->count()) {

        if ($configs->first()->replace_existing) {
            DB::table('task_users')->where('task_id', $task->id)->delete();
        }

        foreach ($configs as $conf) {
            if ($conf->user_id) {
                DB::table('task_users')->updateOrInsert([
                    'task_id' => $task->id,
                    'user_id' => $conf->user_id
                ]);
            }
        }
        return;
    }

    // CASE 2: No assigned users → fallback
    $rule = optional($configs->first())->fallback_rule ?? 'keep';

    switch ($rule) {

        case 'mover':
            DB::table('task_users')->updateOrInsert([
                'task_id' => $task->id,
                'user_id' => $movedByUserId
            ]);
            break;

        case 'random':
            $user = User::whereHas('projects', function($q) use ($task) {
                $q->where('projects.id', $task->project_id);
            })->inRandomOrder()->first();

            if ($user) {
                DB::table('task_users')->updateOrInsert([
                    'task_id' => $task->id,
                    'user_id' => $user->id
                ]);
            }
            break;

        case 'project_owner':
            $ownerId = $task->project->owner_id;
            DB::table('task_users')->updateOrInsert([
                'task_id' => $task->id,
                'user_id' => $ownerId
            ]);
            break;

        case 'keep':
        default:
            // Do nothing
            break;
    }
}
}
