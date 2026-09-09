<?php

namespace App\Http\Controllers;
use App\Project;
use App\ProjectBoard;
use App\LeaveRequest;
use App\Task;
use App\TaskActivity;
use App\User;
use App\WorkspaceSetting;
use Illuminate\Support\Facades\Schema;

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
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        $boards = ProjectBoard::get();
        $projects = Project::with(['parent', 'children.tasks', 'tasks', 'activities', 'comments', 'attachments'])
        ->whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy('name','asc')->where('completed','!=',1)->get();
        $tasks = Task::with(['users', 'project', 'board', 'activities', 'comments', 'attachments', 'feedbackLoops.user', 'feedbackLoops.resolver'])
            ->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            })
            ->where('completed',0)
            ->orderBy('due_date','asc')
            ->get();
        $myActivities = TaskActivity::with(['project', 'task'])
            ->where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->get();
        $users = User::assignableFor(auth()->user());
        $last_sunday = null;
        $saturday = null;
        $members = collect();
        $managerTasks = collect();
        $managerProjects = collect();
        $managerMemberSummaries = collect();
        $managerProjectSummaries = collect();
        $pendingLeaveRequests = collect();
        $isManager = in_array(auth()->user()->role, ['Admin', 'Project Lead'], true);

        if ($isManager) {
            $managerProjects = $projects;
            $projectIds = $managerProjects->pluck('id');

            if ($projectIds->isNotEmpty()) {
                $managerTasks = Task::with(['users', 'project', 'board'])
                    ->whereIn('project_id', $projectIds)
                    ->where('completed', 0)
                    ->orderBy('due_date', 'asc')
                    ->get();

                $managerMembers = User::with(['activities' => function ($query) use ($weekStart, $weekEnd) {
                    $query->whereBetween('date', [$weekStart, $weekEnd]);
                }])
                    ->whereHas('projects', function ($query) use ($projectIds) {
                        $query->whereIn('projects.id', $projectIds);
                    })
                    ->orderBy('name', 'asc')
                    ->get();

                $managerMemberSummaries = $managerMembers->map(function ($member) use ($managerTasks, $today) {
                    $memberTasks = $managerTasks->filter(function ($task) use ($member) {
                        return $task->users->contains('id', $member->id);
                    });

                    return [
                        'user' => $member,
                        'hours' => $member->activities->sum('hours'),
                        'open_tasks' => $memberTasks->count(),
                        'overdue_tasks' => $memberTasks->filter(function ($task) use ($today) {
                            return $task->due_date && $task->due_date < $today;
                        })->count(),
                    ];
                })->sortByDesc(function ($summary) {
                    return ($summary['overdue_tasks'] * 1000) + $summary['open_tasks'];
                })->values();

                $managerProjectSummaries = $managerProjects->map(function ($project) use ($today) {
                    $activeTasks = $project->tasks->where('archived', '!=', 1);
                    $completedTasks = $activeTasks->where('completed', 1)->count();
                    $openTasks = $activeTasks->where('completed', 0);
                    $taskCount = $activeTasks->count();

                    return [
                        'project' => $project,
                        'progress' => $taskCount ? (int) round(($completedTasks / $taskCount) * 100) : 0,
                        'open_tasks' => $openTasks->count(),
                        'overdue_tasks' => $openTasks->filter(function ($task) use ($today) {
                            return $task->due_date && $task->due_date < $today;
                        })->count(),
                    ];
                })->sortByDesc(function ($summary) {
                    return ($summary['overdue_tasks'] * 1000) + $summary['open_tasks'];
                })->values();

            }

            if (Schema::hasTable('leave_requests')) {
                $pendingLeaveQuery = LeaveRequest::with('user')
                    ->where('status', 'Pending')
                    ->where('user_id', '!=', auth()->id());

                if (auth()->user()->role === 'Project Lead') {
                    $workspaceSettings = Schema::hasTable('workspace_settings') ? WorkspaceSetting::first() : null;
                    if ($workspaceSettings && $workspaceSettings->leave_approval_role === 'Admin') {
                        $pendingLeaveQuery->whereRaw('1 = 0');
                    }
                    $reviewableUserIds = $managerMemberSummaries->pluck('user.id');
                    $pendingLeaveQuery->whereIn('user_id', $reviewableUserIds);
                }

                $pendingLeaveRequests = $pendingLeaveQuery->orderBy('start_date', 'asc')->get();
            }
        }

        if (auth()->user()->role == 'Admin') {
            $last_sunday = date('Y-m-d', strtotime('last sunday'));
            $saturday = date('Y-m-d', strtotime('+6 days', strtotime($last_sunday)));
            $members = User::with(['activities' => function ($query) use ($last_sunday, $saturday) {
                $query->whereBetween('date', [$last_sunday, $saturday]);
            }])->get();
        }
        // $project_this_week = Project::with(['activities' => function ($query) use ($last_sunday, $saturday) {
        // $query->whereBetween('date', [$last_sunday, $saturday]);
        // }])
        // ->get()
        // ->map(function ($project) {
        //     return [
        //         'title' => $project->name,
        //         'hours' => number_format($project->activities->sum('hours'), 2, '.', '') // ✅ 2 decimals
        //     ];
        // })
        // ->filter(fn($item) => $item['hours'] > 0)
        // ->sortByDesc('hours')
        // ->values();
        //  if(auth()->user()->role != 'Admin') {
        //    $project_this_week = Project::with(['activities' => function ($query) use ($last_sunday, $saturday) {
        //             $query->whereBetween('date', [$last_sunday, $saturday])
        //                 ->where('user_id', auth()->user()->id);
        //         }])
        //         ->get()
        //         ->map(function ($project) {
        //             return [
        //                 'title' => $project->name,
        //                 'hours' => $project->activities->sum('hours')
        //             ];
        //         })
        //         ->filter(fn($item) => $item['hours'] > 0) // remove projects with 0 hours
        //         ->sortByDesc('hours')
        //         ->values();
        //  }

        // $projects_data = $projects->map(function ($project) {
        //         return [
        //             'title' => $project->name,
        //             'hours' => $project->activities->sum('hours')
        //         ];
        //     })->sortByDesc('hours')
        //     ->filter(fn($item) => $item['hours'] > 0) // Sort by hours in descending order
        //     ->values();
        // dd($task_due);
        return view('home',
            array(
                'projects' => $projects,
                'tasks' => $tasks,
                'myActivities' => $myActivities,
                'members' => $members,
                'last_sunday' => $last_sunday,
                'saturday' => $saturday,
                'boards' => $boards,
                'users' => $users,
                'isManager' => $isManager,
                'managerTasks' => $managerTasks,
                'managerProjects' => $managerProjects,
                'managerMemberSummaries' => $managerMemberSummaries,
                'managerProjectSummaries' => $managerProjectSummaries,
                'pendingLeaveRequests' => $pendingLeaveRequests,
                // 'projects_data' => $projects_data,
                // 'totalHours' => $projects_data->sum('hours'),
                // 'project_this_week' => $project_this_week,

            )
        );
    }
    public function adminDashboard()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

      $paidInvoices = collect(\Stripe\Invoice::all()->data);
    //   dd($paidInvoices);
        $users = User::get();
        $activeUsers = User::whereHas('stripeCustomer', function ($query) {
            $query->where('status', 'active');
        })->get();
        foreach ($activeUsers as $user) {
            $totalPaidCents = 0;

            // Fetch paid invoices for this user
            $invoices = \Stripe\Invoice::all([
                'customer' => $user->stripeCustomer->stripe_customer_id, // stripe customer ID
                'status' => 'paid',
            ])->data;
            // dd($invoices);
            foreach ($invoices as $invoice) {
                $totalPaidCents += $invoice->amount_paid; // Stripe stores in cents
            }
            $user->total_paid = $totalPaidCents / 100;
        }

        $inactiveUsersCount = User::where(function ($query) {
            $query->whereDoesntHave('stripeCustomer')
                ->orWhereHas('stripeCustomer', function ($q) {
                    $q->where('status', '!=', 'active');
                });
        })->count();
        return view('admin.dashboard',
        array(
            'users' => $users,
            'activeUsers' => $activeUsers,
            'inactiveUsersCount' => $inactiveUsersCount,
            'paidInvoices' => $paidInvoices,
        ));

    }
}
