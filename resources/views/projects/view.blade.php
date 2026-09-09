@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .kanban-board {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      /* padding: 20px; */
    }
    .kanban-column {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      flex: 1;
      min-width: 300px;
      display: flex;
      flex-direction: column;
    }
    .kanban-header {
      padding: 15px;
      font-weight: bold;
      border-bottom: 1px solid #dee2e6;
      display: flex;
      justify-content: space-between;
      align-items: center;
  
    }
    .kanban-items::-webkit-scrollbar {
    width: 6px;
}

.kanban-items::-webkit-scrollbar-track {
    background: transparent;
}

.kanban-items::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}
    .kanban-items {
      flex-grow: 1;
      padding: 15px;
      min-height: 400px;
    }
    .kanban-card {
      background: #e9ecef;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 10px;
      cursor: grab;
    }
    .kanban-card.dragging {
      opacity: 0.5;
    }
  </style>
  
<style>
    .kanban-board-container {
        display: flex;
        overflow-x: auto;
        padding: 10px;
    }
    .kanban-board-wrapper {
        display: flex;
        gap: 15px;
        min-height: 40vh;
    }
    .kanban-column {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        min-width: 280px;
        display: flex;
        flex-direction: column;
    }
    .kanban-header {
        background: #e9ecef;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
    }
    .kanban-items {
        flex: 1;
        padding: 10px;
        /* min-height: 100px; */
            max-height: 400px; /* Adjust height based on your layout */
        overflow-y: auto;  /* Vertical scroll only when needed */
        overflow-x: hidden; /* Prevent horizontal scroll */
        padding-right: 8px; /* Space for scrollbar */
        scrollbar-width: thin; /* For Firefox */
        scrollbar-color: #ccc transparent; /* Scrollbar style */
    }
    .kanban-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: grab;
    }
    .kanban-card.dragging {
        opacity: 0.5;
    }
    .subproject-card {
        border: 1px solid #e9ebec;
        border-radius: 8px;
        transition: all .2s ease;
    }
    .subproject-card:hover {
        border-color: #cfd4da;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
        transform: translateY(-2px);
    }
    .subproject-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #eef6ff;
        color: #3577f1;
        font-size: 20px;
    }
    .subproject-stat {
        border: 1px solid #eef0f2;
        border-radius: 8px;
        padding: 10px;
        background: #fafbfc;
    }
</style>
<link href="{{ asset('inside_css/assets/css/saltiii-projects.css') }}" rel="stylesheet" />
@endsection
@section('content')
@php
    $projectTaskCount = $project->tasks->where('archived', '!=', 1)->count();
    $projectCompletedCount = $project->tasks->where('archived', '!=', 1)->where('completed', 1)->count();
    $projectOpenCount = max(0, $projectTaskCount - $projectCompletedCount);
    $projectOverdueCount = $project->tasks->filter(function ($task) {
        return $task->archived != 1 && !$task->completed && $task->due_date && $task->due_date < date('Y-m-d');
    })->count();
    $projectHours = $project->tasks->sum(function ($task) { return $task->activities->sum('hours'); });
    $projectProgress = $projectTaskCount > 0 ? round(($projectCompletedCount / $projectTaskCount) * 100) : 0;
    $firstBoardId = optional($project->statuses->first())->id;
@endphp

<div class="project-workspace">
    <section class="project-overview-hero" aria-labelledby="project-title">
        <div class="project-overview-main">
            <div class="project-identity">
                <span class="project-identity-icon"><img src="{{ asset($project->icon) }}" onerror="this.src='{{ url('images/Favicon.png') }}';" alt=""></span>
                <div>
                    <a href="{{ url('/projects') }}" class="project-back-link"><i class="ri-arrow-left-line" aria-hidden="true"></i> All projects</a>
                    <h1 id="project-title" @if(auth()->user()->role == 'Admin') data-editable-project-name @endif data-id="{{ $project->id }}">{{ $project->name }}</h1>
                    <p class="project-description">{{ $project->description ?: 'No project description has been added yet.' }}</p>
                    <div class="project-meta-row">
                        <span class="project-status-pill">{{ $project->status ?: 'In progress' }}</span>
                        @if($project->parent)<span><i class="ri-git-branch-line" aria-hidden="true"></i> Under <a href="{{ url('/view-project/'.$project->parent->id) }}">{{ $project->parent->name }}</a></span>@endif
                        <span><i class="ri-calendar-line" aria-hidden="true"></i> Started {{ date('M j, Y', strtotime($project->created_at)) }}</span>
                        <span><i class="ri-refresh-line" aria-hidden="true"></i> Updated {{ $project->updated_at->diffForHumans() }}</span>
                        <span><i class="ri-pie-chart-line" aria-hidden="true"></i> {{ $projectProgress }}% complete</span>
                    </div>
                </div>
            </div>
            <div class="project-hero-actions">
                @if($firstBoardId)
                    <button type="button" class="btn btn-primary" onclick="addTask('{{ $firstBoardId }}')"><i class="ri-add-line" aria-hidden="true"></i> New task</button>
                @endif
                @if(auth()->user()->role == 'Admin')
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal" data-bs-target="#addmemberModal"><i class="ri-user-add-line" aria-hidden="true"></i> Team</button>
                    <div class="dropdown">
                        <button class="btn btn-light btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Project actions"><i class="ri-more-2-fill" aria-hidden="true"></i></button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <form method="POST" action="{{ url('/project/complete/'.$project->id) }}" onsubmit="return confirm('Mark this project as complete?');">@csrf<button type="submit" class="dropdown-item"><i class="ri-checkbox-circle-line me-2" aria-hidden="true"></i> Mark complete</button></form>
                            <form method="POST" action="{{ url('/project/delete/'.$project->id) }}" onsubmit="return confirm('Archive this project?');">@csrf<button type="submit" class="dropdown-item text-danger"><i class="ri-archive-line me-2" aria-hidden="true"></i> Archive project</button></form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="project-kpis" aria-label="Project summary">
            <div class="project-kpi"><span><i class="ri-list-check-2" aria-hidden="true"></i></span><div><small>Total tasks</small><strong>{{ $projectTaskCount }}</strong><em>{{ $projectCompletedCount }} completed</em></div></div>
            <div class="project-kpi"><span><i class="ri-loader-4-line" aria-hidden="true"></i></span><div><small>Open tasks</small><strong>{{ $projectOpenCount }}</strong><em>Across {{ $project->statuses->count() }} {{ str_plural('status', $project->statuses->count()) }}</em></div></div>
            <div class="project-kpi {{ $projectOverdueCount ? 'is-alert' : '' }}"><span><i class="ri-alarm-warning-line" aria-hidden="true"></i></span><div><small>Overdue</small><strong>{{ $projectOverdueCount }}</strong><em>Needs attention</em></div></div>
            <div class="project-kpi"><span><i class="ri-time-line" aria-hidden="true"></i></span><div><small>Time logged</small><strong>{{ number_format($projectHours, 1) }}h</strong><em>Across this project</em></div></div>
        </div>
        <ul class="nav project-workspace-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tasks-overview" role="tab" aria-controls="tasks-overview" aria-selected="true">Board <span class="badge bg-primary-subtle text-primary ms-1">{{ $projectTaskCount }}</span></a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sub-projects" role="tab" aria-controls="sub-projects" aria-selected="false">Sub-projects <span class="badge bg-primary-subtle text-primary ms-1" id="subProjectsCountBadge">{{ $project->children->count() }}</span></a></li>
        </ul>
    </section>

 <div class="row project-legacy-header">
    <div class="col-lg-12">
        <div class="card mt-n4 mx-n4">
            <div class="bg-warning-subtle">
                <div class="card-body pb-0 px-4">
                    <div class="row mb-3">
                        <div class="col-md">
                            <div class="row align-items-center g-3">
                                <div class="col-md-auto">
                                    <div class="avatar-md">
                                        <div class="avatar-title bg-white rounded-circle">
                                            <img src="{{asset($project->icon)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="avatar-xs">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 @if(auth()->user()->role == "Admin") id="editable-project-name" @endif class="fw-bold" data-id="{{ $project->id }}">
                                            {{ $project->name }}
                                        </h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            {{-- <div><i class="ri-building-line align-bottom me-1"></i> Themesbrand</div> --}}
                                            {{-- <div class="vr"></div> --}}
                                            <div>Created Date : <span class="fw-medium">{{date('d M, Y',strtotime($project->created_at))}}</span></div>
                                            <div class="vr"></div>
                                            <div>Last Update : <span class="fw-medium">{{date('d M, Y',strtotime($project->updated_at))}}</span></div>
                                            @if($project->parent)
                                                <div class="vr"></div>
                                                <div>
                                                    Parent :
                                                    <a href="{{ url('/view-project/'.$project->parent->id) }}" class="fw-medium">
                                                        {{ $project->parent->name }}
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="vr"></div>
                                            {{-- <div class="badge rounded-pill bg-info fs-12">New</div> --}}
                                            <div class="badge rounded-pill bg-danger fs-12">High</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-auto">
                            <div class="hstack gap-1 flex-wrap">
                                <button type="button" class="btn py-0 fs-16 favourite-btn material-shadow-none active">
                                    <i class="ri-star-fill"></i>
                                </button>
                                <button type="button" class="btn py-0 fs-16 text-body material-shadow-none">
                                    <i class="ri-share-line"></i>
                                </button>
                                <button type="button" class="btn py-0 fs-16 text-body material-shadow-none">
                                    <i class="ri-flag-line"></i>
                                </button>
                            </div>
                        </div> --}}
                    </div>

                    <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tasks-overview" role="tab">
                                Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#sub-projects" role="tab">
                                Sub-projects
                                <span class="badge bg-primary-subtle text-primary ms-1">{{ $project->children->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-comments" role="tab">
                                Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-activities" role="tab">
                                Activities
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- end card body -->
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<div class="row project-tab-shell">
    <div class="col-lg-12">
        <div class="tab-content text-muted">
            <div class="tab-pane fade show active" id="tasks-overview" role="tabpanel">
                <div class="project-board-toolbar">
                    <div class="project-board-actions">
                        @if($firstBoardId)<button type="button" class="btn btn-primary" onclick="addTask('{{ $firstBoardId }}')"><i class="ri-add-line" aria-hidden="true"></i> New task</button>@endif
                        @if(auth()->user()->role == 'Admin')<button type="button" class="btn btn-soft-primary" data-bs-toggle="modal" data-bs-target="#createboardModal"><i class="ri-layout-column-line" aria-hidden="true"></i> New status</button>@endif
                    </div>
                    <div class="project-board-tools">
                        <div class="board-scroll-controls" aria-label="Scroll project board">
                            <button type="button" class="btn btn-light btn-icon" id="boardScrollLeft" aria-label="Scroll board left" title="Scroll left"><i class="ri-arrow-left-line" aria-hidden="true"></i></button>
                            <button type="button" class="btn btn-light btn-icon" id="boardScrollRight" aria-label="Scroll board right" title="Scroll right"><i class="ri-arrow-right-line" aria-hidden="true"></i></button>
                        </div>
                        <div class="search-box">
                            <input type="search" class="form-control search" id="search-task-options" placeholder="Search this board" aria-label="Search tasks in this project" autocomplete="off">
                            <i class="ri-search-line search-icon" aria-hidden="true"></i>
                        </div>
                        <span class="project-team-label">{{ $project->users->count() }} {{ str_plural('member', $project->users->count()) }}</span>
                        <div class="avatar-group" id="newMembar">
                            @foreach($project->users->take(5) as $member)
                                <span class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $member->name }}"><img src="{{ asset($member->avatar) }}" onerror="this.src='{{ url('images/Favicon.png') }}';" alt="{{ $member->name }}" class="rounded-circle avatar-xs"></span>
                            @endforeach
                            @if($project->users->count() > 5)<span class="avatar-group-item"><span class="avatar-xs"><span class="avatar-title rounded-circle bg-light text-muted">+{{ $project->users->count() - 5 }}</span></span></span>@endif
                        </div>
                    </div>
                </div>
                  <div class="kanban-board-container">
                      <div class="kanban-board-wrapper" id="kanbanBoard">
                          @forelse($boardData as $column)
                              <section class="kanban-column" data-id="{{ $column['id'] }}" @if(auth()->user()->role === 'Admin') draggable="true" @endif>
                                  <header class="kanban-header">
                                      <span class="kanban-column-title" id="status-name-{{ $column['id'] }}">{{ $column['name'] }} <span class="kanban-column-count">{{ $column['tasks']->count() }}</span></span>
                                      @if(auth()->user()->role === 'Admin')
                                          <div>
                                              <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="editStatus('{{ $column['id'] }}')" title="Edit status" aria-label="Edit {{ $column['name'] }} status"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                                              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteStatus('{{ $column['id'] }}')" title="Delete status" aria-label="Delete {{ $column['name'] }} status"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                          </div>
                                      @endif
                                  </header>
                                  <div class="kanban-items" ondragover="allowDrop(event)" ondrop="dropTask(event, '{{ $column['id'] }}')">
                                      @foreach($column['tasks'] as $task)
                                          @php
                                              $taskPriority = $task['priority'] ?: 'Low';
                                              $taskIsOverdue = $task['due_date'] && $task['due_date'] < date('Y-m-d') && (int) $task['completed'] === 0;
                                          @endphp
                                          <article id="task-{{ $task['id'] }}" class="kanban-card tasks-box task-card" draggable="true" ondragstart="dragTask(event)">
                                              <div class="card-body" onclick="window.location.href='{{ url('/view-task/'.$task['id']) }}'" onkeydown="if(event.key === 'Enter'){ window.location.href='{{ url('/view-task/'.$task['id']) }}'; }" role="link" tabindex="0">
                                                  <div class="task-card-heading"><span class="task-card-title">@if((int) $task['completed'] === 1)<i class="ri-checkbox-circle-fill text-success me-1" aria-hidden="true"></i>@endif{{ $task['name'] }}</span><span class="task-id">#{{ $task['id'] }}</span></div>
                                                  <div class="task-card-meta"><span class="task-due {{ $taskIsOverdue ? 'is-overdue' : '' }}"><i class="{{ $taskIsOverdue ? 'ri-alarm-warning-line' : 'ri-calendar-line' }}" aria-hidden="true"></i>{{ $task['due_date'] ?: 'No due date' }}</span><span class="task-priority task-priority-{{ strtolower($taskPriority) }}">{{ $taskPriority }}</span></div>
                                                  @if(count($task['assignees']))
                                                      <div class="task-card-assignees">
                                                          @foreach(array_slice($task['assignees'], 0, 4) as $assignee)<span title="{{ $assignee }}">{{ strtoupper(substr($assignee, 0, 1)) }}</span>@endforeach
                                                          @if(count($task['assignees']) > 4)<span>+{{ count($task['assignees']) - 4 }}</span>@endif
                                                      </div>
                                                  @endif
                                              </div>
                                              <footer class="card-footer"><div class="task-card-foot"><span><i class="ri-question-answer-line" aria-hidden="true"></i>{{ $task['comments'] }}</span><span><i class="ri-attachment-2" aria-hidden="true"></i>{{ $task['attachments'] }}</span><span class="task-hours"><i class="ri-time-line" aria-hidden="true"></i>{{ number_format($task['hours'], 1) }}h</span></div></footer>
                                              @if((int) $task['completed'] === 1)<button type="button" class="btn btn-sm btn-outline-secondary archive-task-btn" onclick="event.stopPropagation(); archiveTask({{ $task['id'] }})"><i class="ri-archive-2-line" aria-hidden="true"></i> Archive</button>@endif
                                          </article>
                                      @endforeach
                                  </div>
                                  <div class="kanban-add-task"><button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="addTask('{{ $column['id'] }}')"><i class="ri-add-line" aria-hidden="true"></i> Add task</button></div>
                              </section>
                          @empty
                              <div class="project-board-empty"><div><i class="ri-layout-column-line fs-32 mb-2 d-block" aria-hidden="true"></i><strong class="d-block text-body mb-1">No statuses yet</strong><span>Create a status to begin organizing project tasks.</span></div></div>
                          @endforelse
                      </div>
                  </div>
                  <!-- Modals -->
                 
                  <div class="modal fade" id="statusModal" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit status</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editBoardForm" method="POST" action="{{ url('project/edit-board') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="statusId" id="statusId">
                                <div class="mb-3">
                                    <label for="statusName" class="form-label">Status name</label>
                                    <input type="text" name="statusName" class="form-control" id="statusName" maxlength="80" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save status</button>
                            </div>
                        </form>
                      </div>
                    </div>
                  </div>
             </div>
             <div class="tab-pane fade" id="sub-projects" role="tabpanel">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">Sub-projects</h5>
                            <p class="text-muted mb-0">View all projects inside {{ $project->name }}.</p>
                        </div>
                        <a data-bs-toggle="modal" data-bs-target="#projectModal" class="btn btn-soft-primary">
                            <i class="ri-add-line align-bottom me-1"></i> Add Sub-project
                        </a>
                    </div>
                    <div class="card-body" id="subProjectsPanelBody">
                        @if($project->children->count())
                            <div class="row g-3" id="subProjectsGrid">
                                @foreach($project->children as $subproject)
                                    @php
                                        $subprojectTaskCount = $subproject->tasks->count();
                                        $subprojectCompletedTasks = $subproject->tasks->where('completed', 1)->count();
                                        $subprojectOpenTasks = $subprojectTaskCount - $subprojectCompletedTasks;
                                        $subprojectProgress = $subprojectTaskCount > 0 ? round(($subprojectCompletedTasks / $subprojectTaskCount) * 100) : 0;
                                        $subprojectHours = $subproject->tasks->sum(function ($task) {
                                            return $task->activities->sum('hours');
                                        });
                                    @endphp
                                    <div class="col-xl-4 col-md-6">
                                        <a href="{{ url('/view-project/'.$subproject->id) }}" class="text-decoration-none text-body">
                                            <div class="subproject-card h-100 bg-white p-3">
                                                <div class="d-flex align-items-start gap-3 mb-3">
                                                    <div class="subproject-icon flex-shrink-0">
                                                        <i class="ri-folder-5-line"></i>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                                            <h5 class="fs-15 mb-1 text-truncate">{{ $subproject->name }}</h5>
                                                            <span class="badge bg-success-subtle text-success">{{ $subprojectProgress }}%</span>
                                                        </div>
                                                        <p class="text-muted mb-0 text-truncate-two-lines">
                                                            {{ $subproject->description ?: 'No description added yet.' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="progress progress-sm animated-progress mb-3">
                                                    <div class="progress-bar bg-success" style="width: {{ $subprojectProgress }}%;" role="progressbar" aria-valuenow="{{ $subprojectProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>

                                                <div class="row g-2 mb-3">
                                                    <div class="col-4">
                                                        <div class="subproject-stat">
                                                            <div class="fs-16 fw-semibold text-body">{{ $subprojectTaskCount }}</div>
                                                            <div class="fs-12 text-muted">Tasks</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="subproject-stat">
                                                            <div class="fs-16 fw-semibold text-body">{{ $subprojectOpenTasks }}</div>
                                                            <div class="fs-12 text-muted">Open</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="subproject-stat">
                                                            <div class="fs-16 fw-semibold text-body">{{ number_format($subprojectHours, 1) }}</div>
                                                            <div class="fs-12 text-muted">Hours</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="avatar-group">
                                                        @foreach($subproject->users->take(4) as $member)
                                                            <span class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $member->name }}">
                                                                <img src="{{ asset($member->avatar) }}" onerror="this.src='{{ url('images/Favicon.png') }}';" alt="" class="rounded-circle avatar-xs">
                                                            </span>
                                                        @endforeach
                                                        @if($subproject->users->count() > 4)
                                                            <span class="avatar-group-item material-shadow">
                                                                <span class="avatar-xs">
                                                                    <span class="avatar-title rounded-circle bg-light text-muted">+{{ $subproject->users->count() - 4 }}</span>
                                                                </span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="text-primary fw-medium">
                                                        Open <i class="ri-arrow-right-line align-bottom"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5" id="subProjectsEmptyState">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                                        <i class="ri-folder-add-line"></i>
                                    </div>
                                </div>
                                <h5>No sub-projects yet</h5>
                                <p class="text-muted mb-3">Create a project under {{ $project->name }} to organize related work here.</p>
                                <a data-bs-toggle="modal" data-bs-target="#projectModal" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Add Sub-project
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>

</div>
@include('projects.new-board')
@include('projects.add_member')
@include('projects.add_task')
@include('projects.add_project')
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    
    // Initialize Select2 inside modals
    $('.modal').on('shown.bs.modal', function () {
    let $select = $(this).find('.select2');

    $select.select2({
        dropdownParent: $(this),
        templateResult: function (data) {
            if (!data.id) return data.text; // placeholder

            // Get current selected values
            let selectedValues = $select.val() || [];

            // Hide from list if already selected
            if (selectedValues.includes(data.id)) {
                return null;
            }

            return data.text;
        }
    }).on('change', function () {
        // Force Select2 to re-render results without flicker
        $select.select2('destroy').select2({
            dropdownParent: $(this).closest('.modal'),
            templateResult: function (data) {
                if (!data.id) return data.text;
                let selectedValues = $select.val() || [];
                if (selectedValues.includes(data.id)) {
                    return null;
                }
                return data.text;
            }
        });
    });
  });

        

    
  });
</script>
<script>
        function getInitials(name) {
            const words = name.trim().split(' ');
            let initials = words[0].charAt(0).toUpperCase();
            if (words.length > 1) {
                initials += words[1].charAt(0).toUpperCase();
            }
            return initials;
        }
</script>


<script>
    const boardSearch = document.getElementById('search-task-options');
    if (boardSearch) boardSearch.addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase().trim();

    // Loop through each kanban column
    document.querySelectorAll('.kanban-items').forEach(column => {
        const tasks = column.querySelectorAll('.task-card');
        let hasVisibleTasks = false;

        tasks.forEach(task => {
            const taskText = task.innerText.toLowerCase();

            if (taskText.includes(searchValue)) {
                task.style.display = ''; // Show matching task
                hasVisibleTasks = true;
            } else {
                task.style.display = 'none'; // Hide non-matching task
            }
        });

        // Optional: Show "No tasks found" message if all tasks are hidden
        let noTaskMsg = column.querySelector('.no-tasks-msg');

        if (!hasVisibleTasks) {
            if (!noTaskMsg) {
                noTaskMsg = document.createElement('div');
                noTaskMsg.className = 'no-tasks-msg text-muted text-center p-2';
                noTaskMsg.innerText = 'No matching tasks';
                column.appendChild(noTaskMsg);
            }
        } else if (noTaskMsg) {
            noTaskMsg.remove();
        }
    });
});
    let boardData = @json($boardData); // Laravel data for boards and tasks

    // ====== Render the whole board ======
    function renderBoard() {
        const board = document.getElementById('kanbanBoard');
        if (!board) return;

        if (!boardData.length) {
            board.innerHTML = '<div class="project-board-empty"><div><i class="ri-layout-column-line fs-32 mb-2 d-block"></i><strong class="d-block text-body mb-1">No statuses yet</strong><span>Create a status to begin organizing project tasks.</span></div></div>';
            return;
        }

        const fragment = document.createDocumentFragment();
        boardData.forEach(column => {
              const isAdmin = @json(auth()->user()->role === 'Admin');
            const columnDiv = document.createElement('div');
            columnDiv.className = 'kanban-column';
                 if (isAdmin) {
            columnDiv.setAttribute('draggable', 'true'); // allow column to be dragged
              }
            columnDiv.dataset.id = column.id;

            columnDiv.innerHTML = `
                <div class="kanban-header">
                    <span class="kanban-column-title" id="status-name-${column.id}">${escapeHtml(column.name)} <span class="kanban-column-count">${column.tasks.length}</span></span>
                    
                    <div>
                        <!-- Edit button (visible to all) -->
                           @if(auth()->user()->role == 'Admin')
                        <button class="btn btn-sm btn-outline-secondary me-1" 
                                onclick="editStatus('${column.id}')" 
                                data-bs-toggle="tooltip" 
                                title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <!-- Delete button (only for admins) -->
                     
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteStatus('${column.id}')" 
                                    data-bs-toggle="tooltip" 
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="kanban-items" ondragover="allowDrop(event)" ondrop="dropTask(event, '${column.id}')">
                    ${column.tasks.map(task => renderTask(task)).join('')}
                </div>

                <div class="kanban-add-task">
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="addTask('${column.id}')"><i class="ri-add-line"></i> Add task</button>
                </div>
            `;

            fragment.appendChild(columnDiv);
        });

        board.innerHTML = '';
        board.appendChild(fragment);

        enableColumnDrag(); // enable dragging for columns
    }

    // ====== Render a single task card ======
    function renderTask(task) {
        const today = new Date().toISOString().split('T')[0];
        const isOverdue = task.due_date && task.due_date < today && Number(task.completed) === 0;
        const priority = task.priority || 'Low';
        const assignees = (task.assignees || []).slice(0, 4).map(function (name) {
            return `<span title="${escapeHtml(name)}">${escapeHtml(getInitials(name))}</span>`;
        }).join('');
        const extraAssignees = task.assignees && task.assignees.length > 4
            ? `<span>+${task.assignees.length - 4}</span>` : '';

        return `
            <div id="task-${task.id}" class="kanban-card tasks-box task-card" draggable="true" ondragstart="dragTask(event)">
                <div class="card-body" onclick="window.location.href='{{ url('/view-task') }}/${task.id}'" onkeydown="if(event.key === 'Enter'){ window.location.href='{{ url('/view-task') }}/${task.id}'; }" role="link" tabindex="0">
                    <div class="task-card-heading">
                        <span class="task-card-title">${Number(task.completed) === 1 ? '<i class="ri-checkbox-circle-fill text-success me-1"></i>' : ''}${escapeHtml(task.name)}</span>
                        <span class="task-id">#${task.id}</span>
                    </div>
                    <div class="task-card-meta">
                        <span class="task-due ${isOverdue ? 'is-overdue' : ''}"><i class="${isOverdue ? 'ri-alarm-warning-line' : 'ri-calendar-line'}"></i>${task.due_date || 'No due date'}</span>
                        <span class="task-priority task-priority-${String(priority).toLowerCase()}">${escapeHtml(priority)}</span>
                    </div>
                    ${assignees || extraAssignees ? `<div class="task-card-assignees">${assignees}${extraAssignees}</div>` : ''}
                </div>
                <div class="card-footer">
                    <div class="task-card-foot">
                        <span><i class="ri-question-answer-line"></i>${task.comments}</span>
                        <span><i class="ri-attachment-2"></i>${task.attachments}</span>
                        <span class="task-hours"><i class="ri-time-line"></i>${Number(task.hours || 0).toFixed(1)}h</span>
                    </div>
                </div>
                ${Number(task.completed) === 1 ? `<button class="btn btn-sm btn-outline-secondary archive-task-btn" onclick="event.stopPropagation(); archiveTask(${task.id})"><i class="ri-archive-2-line"></i> Archive</button>` : ''}
            </div>
        `;
    }

    // ====== Enable column dragging ======
    function enableColumnDrag() {
        const columns = document.querySelectorAll('.kanban-column');

        columns.forEach(col => {
            col.addEventListener('dragstart', dragColumnStart);
            col.addEventListener('dragover', allowDrop);
            col.addEventListener('drop', dropColumn);
        });
    }

    // Track the column being dragged
    let draggedColumnId = null;

    function dragColumnStart(event) {
        draggedColumnId = event.target.dataset.id;
        event.dataTransfer.effectAllowed = 'move';
    }

    function dropColumn(event) {
        event.preventDefault();

        const targetColumnId = event.target.closest('.kanban-column').dataset.id;
        if (!draggedColumnId || draggedColumnId === targetColumnId) return;

        // Reorder columns in local data
        const draggedIndex = boardData.findIndex(col => col.id == draggedColumnId);
        const targetIndex = boardData.findIndex(col => col.id == targetColumnId);

        const [movedColumn] = boardData.splice(draggedIndex, 1);
        boardData.splice(targetIndex, 0, movedColumn);

        // Send order to backend
        $.ajax({
            url: "{{ url('/update-column-order') }}",
            method: 'POST',
            data: {
                order: boardData.map(col => col.id),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Toastify({
                  text: "Column order updated",
                  duration: 3000,
                  close: true,
                  gravity: "top", // top or bottom
                  position: "right", // left, center or right
                  backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                  stopOnFocus: true,
              }).showToast();
            },
            error: function (xhr) {
                Toastify({
                    text: "Error updating. Please try again.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    stopOnFocus: true,
                }).showToast();
            }
        });

        renderBoard();
    }

    // ====== Task Dragging ======
    function dragTask(ev) {
        ev.dataTransfer.setData('text/plain', ev.target.id);
    }

    function allowDrop(ev) {
        ev.preventDefault();
    }

    function dropTask(ev, columnId) {
        ev.preventDefault();
        const draggedTaskId = ev.dataTransfer.getData('text/plain').replace('task-', '');

        if (!draggedTaskId || !columnId) return;

        // Move task locally
        let movedTask = null;
        boardData.forEach(col => {
            const index = col.tasks.findIndex(t => t.id == draggedTaskId);
            if (index > -1) {
                movedTask = col.tasks.splice(index, 1)[0];
            }
        });

        if (movedTask) {
            const targetCol = boardData.find(col => col.id == columnId);
            if (targetCol) targetCol.tasks.push(movedTask);
        }

        // Update on server
        $.ajax({
            url: "{{ url('/update-task-column') }}",
            method: 'POST',
            data: {
                task_id: draggedTaskId,
                project_board_id: columnId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Toastify({
                  text: "Task updated successfully!",
                  duration: 3000,
                  close: true,
                  gravity: "top", // top or bottom
                  position: "right", // left, center or right
                  backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                  stopOnFocus: true,
              }).showToast();
            },
            error: function (xhr) {
                Toastify({
                    text: "Error updating task. Please try again.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    stopOnFocus: true,
                }).showToast();
            }
        });

        renderBoard();
    }

    // ====== Status CRUD ======
    function addStatus() {
        document.getElementById('statusId').value = '';
        document.getElementById('statusName').value = '';
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }

    function saveStatus() {
        const id = document.getElementById('statusId').value;
        const name = document.getElementById('statusName').value;
        if (!name) return alert('Please enter status name');

        const existing = boardData.find(c => c.id == id);
        if (existing) {
            existing.name = name;
        } else {
            boardData.push({ id: Date.now().toString(), name, tasks: [] });
        }
        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
        renderBoard();
    }

    function editStatus(id) {
        const column = boardData.find(c => c.id == id);
        document.getElementById('statusId').value = column.id;
        document.getElementById('statusName').value = column.name;
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }

   function deleteStatus(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the status and its tasks!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: "{{ url('/statuses') }}/" + id, // Laravel style URL
                method: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                },
                success: function(response) {
                    if (response.success) {
                        // Remove column from local boardData
                        boardData = boardData.filter(c => c.id != id);
                        renderBoard();

                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The status has been deleted.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to delete the status.',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the status.',
                        icon: 'error'
                    });
                    console.error(xhr.responseText);
                }
            });

        }
    });
  }

      // ====== Tasks ======
      function addTask(columnId) {
          document.getElementById('taskColumn').value = columnId;
          new bootstrap.Modal(document.getElementById('creatertaskModal')).show();
      }
  function archiveTask(taskId) {
    const archiveUrl = `{{ url('tasks') }}/${taskId}/archive`;
      // Show SweetAlert confirmation dialog
      Swal.fire({
          title: "Are you sure?",
          text: "This task will be archived and moved out of the active board.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, archive it!"
      }).then((result) => {
          if (result.isConfirmed) {
              // Send AJAX request using fetch
              fetch(archiveUrl, {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                  }
              })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Remove task from UI
                      document.getElementById(`task-${data.task_id}`).remove();

                      // Show success Toastify
                      Toastify({
                          text: "Task archived successfully!",
                          duration: 3000,
                          gravity: "top",
                          position: "right",
                          backgroundColor: "#28a745",
                          stopOnFocus: true
                      }).showToast();
                  } else {
                      // Show error Toastify
                      Toastify({
                          text: "Failed to archive task.",
                          duration: 3000,
                          gravity: "top",
                          position: "right",
                          backgroundColor: "#dc3545",
                          stopOnFocus: true
                      }).showToast();
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  Toastify({
                      text: "An error occurred. Please try again later.",
                      duration: 3000,
                      gravity: "top",
                      position: "right",
                      backgroundColor: "#dc3545",
                      stopOnFocus: true
                  }).showToast();
              });
          }
      });
  }


    // Initial render
    try {
        renderBoard();
    } catch (error) {
        console.error('Unable to enhance the project board. Server-rendered board retained.', error);
    }

    (function enableBoardScrolling() {
        const scroller = document.querySelector('.kanban-board-container');
        const leftButton = document.getElementById('boardScrollLeft');
        const rightButton = document.getElementById('boardScrollRight');
        if (!scroller || !leftButton || !rightButton) return;

        function updateScrollButtons() {
            const maximum = Math.max(0, scroller.scrollWidth - scroller.clientWidth);
            leftButton.disabled = scroller.scrollLeft <= 2;
            rightButton.disabled = scroller.scrollLeft >= maximum - 2;
        }

        leftButton.addEventListener('click', function () {
            scroller.scrollBy({ left: -340, behavior: 'smooth' });
        });
        rightButton.addEventListener('click', function () {
            scroller.scrollBy({ left: 340, behavior: 'smooth' });
        });
        scroller.addEventListener('scroll', updateScrollButtons, { passive: true });
        window.addEventListener('resize', updateScrollButtons);
        updateScrollButtons();
    }());
</script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
  $(document).ready(function() {
      let originalText = '';

      // Use event delegation so double-click works even after replacement
      $(document).on('dblclick', '[data-editable-project-name]', function() {
          let $this = $(this);
          let currentText = $this.text().trim();
          originalText = currentText; // Store original text

          // Replace the h4 with an input
          let input = $('<input>', {
              type: 'text',
              class: 'form-control form-control-sm',
              value: currentText,
              id: 'project-name-input'
          });

          $this.replaceWith(input);
          input.focus().select();

          // Save on Enter key
          input.on('keypress', function(e) {
              if (e.which === 13) { // Enter key
                  saveProjectName(input, $this.data('id'));
              }
          });

          // Save on blur (optional)
          input.on('blur', function() {
              saveProjectName(input, $this.data('id'));
          });
      });

      function saveProjectName(input, projectId) {
          let newName = input.val().trim();

          if (newName === '') {
              revertToText(originalText, projectId);
              return;
          }

          $.ajax({
              url: "{{ url('/project/edit/') }}/" + projectId,
              method: 'POST', // using POST instead of PUT
              data: {
                  _token: '{{ csrf_token() }}',
                  name: newName
              },
              success: function(response) {
                  revertToText(response.name, projectId);

                  Toastify({
                      text: "Project name updated successfully!",
                      duration: 3000,
                      gravity: "top",
                      position: "right",
                      backgroundColor: "#4CAF50",
                      close: true
                  }).showToast();
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
                  revertToText(originalText, projectId);

                  let message = "Error updating project name. Please try again.";

                  if (xhr.responseJSON && xhr.responseJSON.errors) {
                      message = Object.values(xhr.responseJSON.errors)[0][0];
                  }

                  Toastify({
                      text: message,
                      duration: 4000,
                      gravity: "top",
                      position: "right",
                      backgroundColor: "#F44336",
                      close: true
                  }).showToast();
              }
          });
      }

      function revertToText(name, projectId) {
          let newH4 = $('<h1>', {
              id: 'project-title',
              'data-editable-project-name': '',
              'data-id': projectId,
              text: name
          });

          $('#project-name-input').replaceWith(newH4);
      }
  });
</script>
<script>
$(document).ready(function() {
    $('#editBoardForm').on('submit', function(e) {
        e.preventDefault(); // Prevent page refresh

        let formData = $(this).serialize();
        const statusId = $('#statusId').val();
        const statusName = $('#statusName').val();
        $.ajax({
            url: "{{ url('project/edit-board') }}", // ✅ Manual URL
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // ✅ CSRF Token
                statusId: $('#statusId').val(),
                statusName: $('#statusName').val()
            },
            success: function(response) {
                if (response.success) {
                    const updatedColumn = boardData.find(column => String(column.id) === String(statusId));
                    if (updatedColumn) updatedColumn.name = statusName;
                    renderBoard();
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('statusModal')).hide();

                    // Show success toast
                    Toastify({
                        text: response.message || "Status updated successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                    }).showToast();
                } else {
                    // Show error toast
                    $(`#status-name-${statusId}`).text(statusName);
                    Toastify({
                        text: response.message || "Failed to update status.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                    }).showToast();
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Toastify({
                    text: "An error occurred while saving the status.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        });
    });
});
</script>
<script>
$(document).ready(function() {
    $('#subProjectForm').on('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitButton = $(form).find('button[type="submit"]');
        const originalButtonText = submitButton.html();

        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving');

        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (!response.success || !response.project) {
                    showSubProjectToast(response.message || 'Unable to create sub-project.', '#dc3545');
                    return;
                }

                appendSubProjectCard(response.project);
                incrementSubProjectCount();

                bootstrap.Modal.getOrCreateInstance(document.getElementById('projectModal')).hide();
                resetSubProjectForm(form);
                showSubProjectToast(response.message || 'Sub-project created successfully.', '#28a745');
            },
            error: function(xhr) {
                let message = 'Unable to create sub-project. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstError = Object.values(xhr.responseJSON.errors)[0];
                    message = Array.isArray(firstError) ? firstError[0] : firstError;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                showSubProjectToast(message, '#dc3545');
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
                $('#loader').hide();
            }
        });
    });
});

function appendSubProjectCard(project) {
    let grid = $('#subProjectsGrid');

    if (!grid.length) {
        $('#subProjectsEmptyState').remove();
        $('#subProjectsPanelBody').html('<div class="row g-3" id="subProjectsGrid"></div>');
        grid = $('#subProjectsGrid');
    }

    grid.prepend(renderSubProjectCard(project));
}

function renderSubProjectCard(project) {
    const description = escapeHtml(project.description || 'No description added yet.');
    const members = (project.users || []).slice(0, 4).map(function(member) {
        return `
            <span class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="${escapeHtml(member.name)}">
                <img src="${member.avatar}" onerror="this.src='{{ url('images/Favicon.png') }}';" alt="" class="rounded-circle avatar-xs">
            </span>
        `;
    }).join('');

    const extraMembers = project.users && project.users.length > 4
        ? `<span class="avatar-group-item material-shadow"><span class="avatar-xs"><span class="avatar-title rounded-circle bg-light text-muted">+${project.users.length - 4}</span></span></span>`
        : '';

    return `
        <div class="col-xl-4 col-md-6">
            <a href="${project.url}" class="text-decoration-none text-body">
                <div class="subproject-card h-100 bg-white p-3">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="subproject-icon flex-shrink-0">
                            <i class="ri-folder-5-line"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <h5 class="fs-15 mb-1 text-truncate">${escapeHtml(project.name)}</h5>
                                <span class="badge bg-success-subtle text-success">${project.progress}%</span>
                            </div>
                            <p class="text-muted mb-0 text-truncate-two-lines">${description}</p>
                        </div>
                    </div>
                    <div class="progress progress-sm animated-progress mb-3">
                        <div class="progress-bar bg-success" style="width: ${project.progress}%;" role="progressbar" aria-valuenow="${project.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="subproject-stat">
                                <div class="fs-16 fw-semibold text-body">${project.tasks}</div>
                                <div class="fs-12 text-muted">Tasks</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="subproject-stat">
                                <div class="fs-16 fw-semibold text-body">${project.open_tasks}</div>
                                <div class="fs-12 text-muted">Open</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="subproject-stat">
                                <div class="fs-16 fw-semibold text-body">${Number(project.hours).toFixed(1)}</div>
                                <div class="fs-12 text-muted">Hours</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="avatar-group">${members}${extraMembers}</div>
                        <span class="text-primary fw-medium">Open <i class="ri-arrow-right-line align-bottom"></i></span>
                    </div>
                </div>
            </a>
        </div>
    `;
}

function incrementSubProjectCount() {
    const badge = $('#subProjectsCountBadge');
    badge.text((parseInt(badge.text(), 10) || 0) + 1);
}

function resetSubProjectForm(form) {
    const $form = $(form);
    const parentId = $form.find('[name="parent_id"]').val();
    const selectedMembers = $form.find('[name="team_member[]"]').val();

    form.reset();
    $form.find('[name="parent_id"]').val(parentId).trigger('change');
    $form.find('[name="team_member[]"]').val(selectedMembers).trigger('change');
}

function showSubProjectToast(message, color) {
    if (typeof Toastify === 'function') {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: color,
            stopOnFocus: true
        }).showToast();
        return;
    }

    alert(message);
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
  <script>
    $('.summernote').summernote({
      height: 250,
      toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link','picture','video']],
        ['view', ['codeview']]
      ]
    });
    // Summernote keeps content in the textarea, so no extra sync needed.
  </script>
@endsection
