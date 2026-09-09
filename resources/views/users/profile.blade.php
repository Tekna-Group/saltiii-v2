@extends('layouts.header')

@section('title', $user->name.' | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-people.css') }}" rel="stylesheet" />
@endsection

@section('content')
@php
    $today = date('Y-m-d');
    $isOwnProfile = auth()->id() === $user->id;
    $openTasks = $tasks->where('completed', 0);
    $overdueTasks = $openTasks->filter(function ($task) use ($today) { return $task->due_date && $task->due_date < $today; });
    $weekHours = $activities->sum('hours');
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(function ($part) { return strtoupper(substr($part, 0, 1)); })->implode('');
@endphp

<div class="people-profile">
    <header class="profile-record-header">
        <div class="profile-identity">
            <span class="profile-avatar"><img src="{{ asset($user->avatar ?: 'images/Favicon.png') }}" onerror="this.src='{{ url('images/Favicon.png') }}';" alt="{{ $user->name }}"></span>
            <div><span class="profile-eyebrow">Employee record</span><h1>{{ $user->name }}</h1><p><span class="profile-role">{{ $user->role ?: 'Member' }}</span><span class="profile-status {{ strtolower($user->status) === 'active' ? 'is-active' : '' }}">{{ $user->status ?: 'Active' }}</span></p></div>
        </div>
        <div class="profile-actions">
            @if($isOwnProfile)
                <button class="btn btn-soft-primary" type="button" data-bs-toggle="modal" data-bs-target="#changeProfilePhoto"><i class="ri-image-edit-line" aria-hidden="true"></i> Change photo</button>
                <a class="btn btn-primary" href="{{ url('/my-timekeeping') }}"><i class="ri-time-line" aria-hidden="true"></i> My timesheet</a>
            @elseif(auth()->user()->role === 'Admin')
                <a class="btn btn-primary" href="{{ url('/users') }}"><i class="ri-user-settings-line" aria-hidden="true"></i> Manage people</a>
            @endif
        </div>
    </header>

    <section class="profile-signal-strip" aria-label="Employee work summary">
        <div><small>Hours this week</small><strong>{{ number_format($weekHours, 1) }}h</strong><span>{{ date('M j', strtotime($last_sunday)) }}–{{ date('M j', strtotime($saturday)) }}</span></div>
        <div><small>Open tasks</small><strong>{{ $openTasks->count() }}</strong><span>Currently assigned</span></div>
        <div class="{{ $overdueTasks->count() ? 'has-alert' : '' }}"><small>Overdue</small><strong>{{ $overdueTasks->count() }}</strong><span>Past due date</span></div>
        <div><small>Projects</small><strong>{{ $projects->count() }}</strong><span>Active memberships</span></div>
    </section>

    <div class="profile-layout">
        <main class="profile-main">
            <section class="profile-section" aria-labelledby="profile-work-title">
                <header><div><span class="profile-eyebrow">Current workload</span><h2 id="profile-work-title">Assigned work</h2></div><a href="{{ $isOwnProfile ? url('/tasks') : '#profile-projects' }}">{{ $isOwnProfile ? 'Open task register' : 'View projects' }} <i class="ri-arrow-right-line" aria-hidden="true"></i></a></header>
                <div class="profile-task-list">
                    @forelse($openTasks->take(6) as $task)
                        @php($isOverdue = $task->due_date && $task->due_date < $today)
                        <a href="{{ url('/view-task/'.$task->id) }}" class="profile-task {{ $isOverdue ? 'is-overdue' : '' }}"><span class="profile-task-mark" aria-hidden="true"></span><span><strong>{{ $task->title }}</strong><small>{{ optional($task->project)->name ?: 'No project' }} · {{ optional($task->board)->board ?: 'Open' }}</small></span><time datetime="{{ $task->due_date }}">{{ $task->due_date ? date('M j', strtotime($task->due_date)) : 'No date' }}</time><i class="ri-arrow-right-s-line" aria-hidden="true"></i></a>
                    @empty
                        <div class="profile-empty"><i class="ri-checkbox-circle-line" aria-hidden="true"></i><span><strong>No open tasks</strong><small>New assignments will appear here.</small></span></div>
                    @endforelse
                </div>
            </section>

            <section class="profile-section" aria-labelledby="profile-activity-title">
                <header><div><span class="profile-eyebrow">Work record</span><h2 id="profile-activity-title">This week’s activity</h2></div></header>
                <div class="profile-week">
                    @for($offset = 0; $offset < 7; $offset++)
                        @php
                            $date = date('Y-m-d', strtotime($last_sunday.' +'.$offset.' days'));
                            $dayActivities = $activities->filter(function ($activity) use ($date) { return date('Y-m-d', strtotime($activity->date)) === $date; });
                        @endphp
                        <div class="profile-day {{ $date === $today ? 'is-today' : '' }}"><span>{{ date('D', strtotime($date)) }}</span><strong>{{ $dayActivities->sum('hours') ? number_format($dayActivities->sum('hours'), 1).'h' : '—' }}</strong><small>{{ date('M j', strtotime($date)) }}</small></div>
                    @endfor
                </div>
                <div class="profile-activity-list">
                    @forelse($activities->sortByDesc('date')->take(8) as $activity)
                        <div class="profile-activity"><time datetime="{{ $activity->date }}">{{ date('M j', strtotime($activity->date)) }}</time><span><strong>{{ $activity->activity }}</strong><small>{{ optional($activity->project)->name ?: 'No project' }} · {{ optional($activity->task)->title ?: 'General work' }}</small></span><em>{{ number_format($activity->hours, 1) }}h</em></div>
                    @empty
                        <div class="profile-compact-empty">No time entries were recorded for this week.</div>
                    @endforelse
                </div>
            </section>

            <section class="profile-section" id="profile-projects" aria-labelledby="profile-projects-title">
                <header><div><span class="profile-eyebrow">Memberships</span><h2 id="profile-projects-title">Active projects</h2></div></header>
                <div class="profile-project-list">
                    @forelse($projects as $project)
                        @php
                            $projectTotal = $project->tasks->count();
                            $projectDone = $project->tasks->where('completed', 1)->count();
                            $projectProgress = $projectTotal ? (int) round(($projectDone / $projectTotal) * 100) : 0;
                        @endphp
                        <a href="{{ url('/view-project/'.$project->id) }}"><span><strong>{{ $project->name }}</strong><small>{{ $project->status ?: 'In progress' }}</small></span><i><b style="width:{{ $projectProgress }}%"></b></i><em>{{ $projectProgress }}%</em><span class="ri-arrow-right-s-line" aria-hidden="true"></span></a>
                    @empty
                        <div class="profile-compact-empty">No active project memberships.</div>
                    @endforelse
                </div>
            </section>
        </main>

        <aside class="profile-sidebar">
            <section class="profile-facts" aria-labelledby="profile-details-title"><header><span class="profile-eyebrow">Account</span><h2 id="profile-details-title">Employee details</h2></header><dl><div><dt>Email</dt><dd>{{ $user->email }}</dd></div><div><dt>Role</dt><dd>{{ $user->role ?: 'Member' }}</dd></div><div><dt>Status</dt><dd>{{ $user->status ?: 'Active' }}</dd></div><div><dt>Joined</dt><dd>{{ date('M j, Y', strtotime($user->created_at)) }}</dd></div>@if($isOwnProfile && $user->wallet_address)<div><dt>Payment wallet</dt><dd class="is-code">{{ $user->wallet_address }}</dd></div>@endif</dl></section>
            @if($isOwnProfile)<nav class="profile-shortcuts" aria-label="Employee shortcuts"><a href="{{ url('/my-payslips') }}"><i class="ri-bank-card-line" aria-hidden="true"></i><span><strong>My pay</strong><small>Payslips and payment history</small></span><i class="ri-arrow-right-s-line" aria-hidden="true"></i></a><a href="{{ url('/leave') }}"><i class="ri-calendar-event-line" aria-hidden="true"></i><span><strong>Leave requests</strong><small>Request or review time off</small></span><i class="ri-arrow-right-s-line" aria-hidden="true"></i></a></nav>@endif
        </aside>
    </div>
</div>

@if($isOwnProfile)
<div class="modal fade" id="changeProfilePhoto" tabindex="-1" aria-labelledby="changeProfilePhotoTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="POST" action="{{ url('/change-avatar/'.$user->id) }}" enctype="multipart/form-data">@csrf<div class="modal-header"><div><h5 class="modal-title" id="changeProfilePhotoTitle">Change profile photo</h5><p class="text-muted mb-0 mt-1 fs-12">Use a clear square JPG, PNG, or WebP image.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><label class="form-label" for="profilePhoto">Photo</label><input class="form-control" id="profilePhoto" type="file" name="file" accept="image/png,image/jpeg,image/webp" required></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save photo</button></div></form></div></div></div>
@endif
@endsection
