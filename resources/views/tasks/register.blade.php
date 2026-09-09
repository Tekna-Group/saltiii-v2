@extends('layouts.header')

@section('title', 'Tasks | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-tasks.css') }}" rel="stylesheet" />
@endsection

@section('content')
@php
    $today = date('Y-m-d');
    $openTasks = $tasks->where('completed', 0);
    $completedTasks = $tasks->where('completed', 1);
    $overdueTasks = $openTasks->filter(function ($task) use ($today) {
        return $task->due_date && $task->due_date < $today;
    });
    $dueTodayTasks = $openTasks->where('due_date', $today);
@endphp

<div class="task-register-page">
    <header class="task-register-intro">
        <div>
            <span class="task-register-eyebrow">Personal work register</span>
            <h1>Tasks, ordered for action.</h1>
            <p>Review deadlines, ownership, and project status without leaving the register.</p>
        </div>
        <a class="btn task-register-project-action" href="{{ url('/projects') }}"><i class="ri-folder-add-line" aria-hidden="true"></i> Open projects</a>
    </header>

    <section class="task-signal-strip" aria-label="Task summary">
        <button type="button" data-quick-filter="open" class="is-active"><small>Open</small><strong>{{ $openTasks->count() }}</strong><span>Assigned to you</span></button>
        <button type="button" data-quick-filter="overdue" class="is-critical"><small>Overdue</small><strong>{{ $overdueTasks->count() }}</strong><span>Past the due date</span></button>
        <button type="button" data-quick-filter="today" class="is-today"><small>Due today</small><strong>{{ $dueTodayTasks->count() }}</strong><span>Needs attention now</span></button>
        <button type="button" data-quick-filter="completed"><small>Completed</small><strong>{{ $completedTasks->count() }}</strong><span>Recorded outcomes</span></button>
    </section>

    <section class="task-register" aria-labelledby="task-register-title">
        <header class="task-register-toolbar">
            <div><span class="task-register-eyebrow">Task directory</span><h2 id="task-register-title">My task register <b id="visibleTaskCount">{{ $openTasks->count() }}</b></h2></div>
            <div class="task-filter-controls">
                <label class="task-search" for="taskSearch"><i class="ri-search-line" aria-hidden="true"></i><input id="taskSearch" type="search" placeholder="Search task, project, or owner" autocomplete="off"><span class="visually-hidden">Search tasks</span></label>
                <label><span class="visually-hidden">Filter by project</span><select id="taskProjectFilter" class="form-select"><option value="all">All projects</option>@foreach($projects->sortBy('name') as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach</select></label>
                <label><span class="visually-hidden">Filter by status</span><select id="taskStatusFilter" class="form-select"><option value="open" selected>Open tasks</option><option value="overdue">Overdue</option><option value="today">Due today</option><option value="completed">Completed</option><option value="all">All statuses</option></select></label>
                <label><span class="visually-hidden">Filter by priority</span><select id="taskPriorityFilter" class="form-select"><option value="all">All priorities</option><option value="high">High priority</option><option value="medium">Medium priority</option><option value="low">Low priority</option></select></label>
            </div>
        </header>

        <div class="task-table-wrap">
            <table class="task-table">
                <thead><tr><th>Task</th><th>Project</th><th>Owner</th><th>Stage</th><th>Due</th><th>Priority</th><th><span class="visually-hidden">Open</span></th></tr></thead>
                <tbody id="taskRows">
                    @foreach($tasks as $task)
                        @php
                            $isCompleted = (int) $task->completed === 1;
                            $isOverdue = !$isCompleted && $task->due_date && $task->due_date < $today;
                            $isToday = !$isCompleted && $task->due_date === $today;
                            $owners = $task->users->pluck('name')->implode(', ');
                            $priority = strtolower($task->priority ?: 'normal');
                            $searchText = strtolower($task->title.' '.optional($task->project)->name.' '.$owners.' '.optional($task->board)->board);
                        @endphp
                        <tr class="{{ $isOverdue ? 'is-overdue' : ($isToday ? 'is-due-today' : '') }}"
                            data-task-row
                            data-search="{{ $searchText }}"
                            data-project="{{ $task->project_id }}"
                            data-priority="{{ $priority }}"
                            data-completed="{{ $isCompleted ? '1' : '0' }}"
                            data-overdue="{{ $isOverdue ? '1' : '0' }}"
                            data-today="{{ $isToday ? '1' : '0' }}">
                            <td><a class="task-name" href="{{ url('/view-task/'.$task->id) }}"><span class="task-row-signal" aria-hidden="true"></span><span><small>#{{ $task->id }}</small><strong>{{ $task->title }}</strong><em>{{ $task->comments->count() }} comments · {{ number_format($task->activities->sum('hours'), 1) }}h logged</em></span></a></td>
                            <td><a class="task-project" href="{{ url('/view-project/'.$task->project_id) }}">{{ optional($task->project)->name ?: 'No project' }}</a></td>
                            <td><div class="task-owners">@forelse($task->users->take(3) as $member)<span title="{{ $member->name }}">{{ strtoupper(substr($member->name, 0, 1)) }}</span>@empty<em>Unassigned</em>@endforelse</div></td>
                            <td><span class="task-stage">{{ optional($task->board)->board ?: ($isCompleted ? 'Completed' : 'Open') }}</span></td>
                            <td><time datetime="{{ $task->due_date }}">{{ $isToday ? 'Today' : ($task->due_date ? date('M j, Y', strtotime($task->due_date)) : 'No date') }}</time></td>
                            <td><span class="task-priority task-priority-{{ $priority }}">{{ $task->priority ?: 'Normal' }}</span></td>
                            <td><a class="task-open" href="{{ url('/view-task/'.$task->id) }}" aria-label="Open {{ $task->title }}"><i class="ri-arrow-right-line" aria-hidden="true"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="task-register-empty" id="taskRegisterEmpty" hidden><i class="ri-search-eye-line" aria-hidden="true"></i><div><strong>No tasks match this view</strong><p>Adjust the filters or choose another task status.</p></div><button type="button" id="clearTaskFilters" class="btn btn-soft-primary">Clear filters</button></div>
    </section>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var rows = Array.prototype.slice.call(document.querySelectorAll('[data-task-row]'));
    var search = document.getElementById('taskSearch');
    var project = document.getElementById('taskProjectFilter');
    var status = document.getElementById('taskStatusFilter');
    var priority = document.getElementById('taskPriorityFilter');
    var empty = document.getElementById('taskRegisterEmpty');
    var count = document.getElementById('visibleTaskCount');

    function matchesStatus(row, value) {
        if (value === 'all') return true;
        if (value === 'open') return row.dataset.completed === '0';
        if (value === 'completed') return row.dataset.completed === '1';
        if (value === 'overdue') return row.dataset.overdue === '1';
        if (value === 'today') return row.dataset.today === '1';
        return true;
    }

    function updateTasks() {
        var query = search.value.trim().toLowerCase();
        var visible = 0;
        rows.forEach(function (row) {
            var show = (!query || row.dataset.search.indexOf(query) !== -1)
                && (project.value === 'all' || row.dataset.project === project.value)
                && (priority.value === 'all' || row.dataset.priority === priority.value)
                && matchesStatus(row, status.value);
            row.hidden = !show;
            if (show) visible++;
        });
        count.textContent = visible;
        empty.hidden = visible !== 0;
        document.querySelectorAll('[data-quick-filter]').forEach(function (button) {
            button.classList.toggle('is-active', button.dataset.quickFilter === status.value);
        });
    }

    [search, project, status, priority].forEach(function (control) {
        control.addEventListener(control === search ? 'input' : 'change', updateTasks);
    });
    document.querySelectorAll('[data-quick-filter]').forEach(function (button) {
        button.addEventListener('click', function () { status.value = button.dataset.quickFilter; updateTasks(); });
    });
    document.getElementById('clearTaskFilters').addEventListener('click', function () {
        search.value = ''; project.value = 'all'; status.value = 'open'; priority.value = 'all'; updateTasks(); search.focus();
    });
    updateTasks();
});
</script>
@endsection
