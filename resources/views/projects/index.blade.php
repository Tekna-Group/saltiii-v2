@extends('layouts.header')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('inside_css/assets/css/saltiii-projects.css') }}" rel="stylesheet" />
@endsection

@section('content')
@php
    $projectCount = $projects->count();
    $taskCount = $projects->sum(function ($project) { return $project->tasks->count(); });
    $completedTaskCount = $projects->sum(function ($project) { return $project->tasks->where('completed', 1)->count(); });
    $openTaskCount = max(0, $taskCount - $completedTaskCount);
    $portfolioProgress = $taskCount > 0 ? round(($completedTaskCount / $taskCount) * 100) : 0;
    $overdueTaskCount = $projects->sum(function ($project) {
        return $project->tasks->filter(function ($task) {
            return !$task->completed && $task->due_date && $task->due_date < date('Y-m-d');
        })->count();
    });
@endphp

<div class="projects-page">
    <section class="projects-hero" aria-labelledby="projects-title">
        <div>
            <span class="projects-eyebrow">Workspace portfolio</span>
            <h1 id="projects-title">Projects that keep work moving.</h1>
            <p>See progress, ownership, and the next tasks across every project assigned to you.</p>
        </div>
        <button type="button" data-bs-toggle="modal" data-bs-target="#projectModal" class="btn projects-primary-action">
            <i class="ri-add-line" aria-hidden="true"></i> New project
        </button>
    </section>

    <section class="projects-summary" aria-label="Project portfolio summary">
        <div class="project-summary-item">
            <span><i class="ri-folder-3-line" aria-hidden="true"></i></span>
            <div><small>Active projects</small><strong>{{ $projectCount }}</strong><em>Assigned to you</em></div>
        </div>
        <div class="project-summary-item">
            <span><i class="ri-list-check-2" aria-hidden="true"></i></span>
            <div><small>Open tasks</small><strong>{{ $openTaskCount }}</strong><em>{{ $completedTaskCount }} completed</em></div>
        </div>
        <div class="project-summary-item {{ $overdueTaskCount ? 'has-attention' : '' }}">
            <span><i class="ri-alarm-warning-line" aria-hidden="true"></i></span>
            <div><small>Needs attention</small><strong>{{ $overdueTaskCount }}</strong><em>Overdue {{ str_plural('task', $overdueTaskCount) }}</em></div>
        </div>
        <div class="project-summary-item">
            <span><i class="ri-pie-chart-line" aria-hidden="true"></i></span>
            <div><small>Overall progress</small><strong>{{ $portfolioProgress }}%</strong><em>Across active work</em></div>
        </div>
    </section>

    <section class="projects-directory" aria-labelledby="directory-title">
        <header class="projects-toolbar">
            <div class="projects-toolbar-copy">
                <span class="projects-eyebrow">Project directory</span>
                <h2 id="directory-title">Your active projects <span id="visibleProjectCount">{{ $projectCount }}</span></h2>
            </div>
            <div class="projects-toolbar-controls">
                <label class="projects-search" for="projectSearch">
                    <i class="ri-search-line" aria-hidden="true"></i>
                    <input type="search" id="projectSearch" placeholder="Search by project or description" autocomplete="off">
                    <span class="visually-hidden">Search projects</span>
                </label>
                <label class="projects-select" for="projectStatusFilter">
                    <span class="visually-hidden">Filter projects by status</span>
                    <select id="projectStatusFilter" class="form-select">
                        <option value="all">All statuses</option>
                        <option value="in progress">In progress</option>
                        <option value="to be started">Not started</option>
                        <option value="on hold">On hold</option>
                    </select>
                </label>
                <label class="projects-select" for="projectSort">
                    <span class="visually-hidden">Sort projects</span>
                    <select id="projectSort" class="form-select">
                        <option value="updated">Recently updated</option>
                        <option value="name">Name A–Z</option>
                        <option value="progress">Highest progress</option>
                    </select>
                </label>
            </div>
        </header>

        @if($projects->count())
            <div class="projects-list-heading" aria-hidden="true"><span></span><span>Project</span><span>Progress</span><span>Work status</span><span>Team</span></div>
            <div class="projects-grid" id="projectsContainer">
                @foreach($projects as $project)
                    @php
                        $total = $project->tasks->count();
                        $completed = $project->tasks->where('completed', 1)->count();
                        $open = max(0, $total - $completed);
                        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
                        $projectOverdue = $project->tasks->filter(function ($task) {
                            return !$task->completed && $task->due_date && $task->due_date < date('Y-m-d');
                        })->count();
                        $status = $project->status ?: 'In Progress';
                        $searchText = strtolower($project->name.' '.($project->description ?: '').' '.$status);
                    @endphp
                    <article class="project-directory-card project-card"
                        data-search="{{ $searchText }}"
                        data-status="{{ strtolower($status) }}"
                        data-updated="{{ strtotime($project->updated_at) }}"
                        data-name="{{ strtolower($project->name) }}"
                        data-progress="{{ $percentage }}">
                        <a href="{{ url('/view-project/'.$project->id) }}" class="project-card-link" aria-label="Open {{ $project->name }}"></a>
                        <div class="project-card-top">
                            <span class="project-card-icon">
                                <img src="{{ asset($project->icon) }}" onerror="this.src='{{ url('images/Favicon.png') }}';" alt="">
                            </span>
                            <span class="project-status project-status-{{ str_slug($status) }}">{{ $status }}</span>
                        </div>
                        <div class="project-card-main">
                            @if($project->parent)
                                <span class="project-parent"><i class="ri-git-branch-line" aria-hidden="true"></i> {{ $project->parent->name }}</span>
                            @elseif($project->children->count())
                                <span class="project-parent"><i class="ri-node-tree" aria-hidden="true"></i> {{ $project->children->count() }} {{ str_plural('sub-project', $project->children->count()) }}</span>
                            @endif
                            <h3 class="project-name">{{ $project->name }}</h3>
                            <p>{{ $project->description ?: 'No description has been added yet.' }}</p>
                        </div>
                        <div class="project-progress-block">
                            <div><span>Progress</span><strong>{{ $percentage }}%</strong></div>
                            <div class="project-progress" role="progressbar" aria-label="{{ $project->name }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percentage }}"><i style="width:{{ $percentage }}%"></i></div>
                        </div>
                        <div class="project-card-stats">
                            <span><strong>{{ $open }}</strong><small>Open tasks</small></span>
                            <span class="{{ $projectOverdue ? 'is-overdue' : '' }}"><strong>{{ $projectOverdue }}</strong><small>Overdue</small></span>
                            <span><strong>{{ $project->users->count() }}</strong><small>{{ str_plural('Member', $project->users->count()) }}</small></span>
                        </div>
                        <footer class="project-card-footer">
                            <div class="project-members" aria-label="Project members">
                                @foreach($project->users->take(4) as $member)
                                    <img src="{{ asset($member->avatar) }}" onerror="this.src='{{ url('images/Favicon.png') }}';" alt="{{ $member->name }}" title="{{ $member->name }}">
                                @endforeach
                                @if($project->users->count() > 4)<span>+{{ $project->users->count() - 4 }}</span>@endif
                            </div>
                            <time datetime="{{ $project->updated_at->toIso8601String() }}">Updated {{ $project->updated_at->diffForHumans() }}</time>
                            <i class="ri-arrow-right-line" aria-hidden="true"></i>
                        </footer>
                    </article>
                @endforeach
            </div>
            <div class="projects-no-results" id="projectNoResults" hidden>
                <span><i class="ri-search-eye-line" aria-hidden="true"></i></span>
                <h3>No matching projects</h3>
                <p>Try a different search term or status filter.</p>
                <button type="button" class="btn btn-soft-primary" id="clearProjectFilters">Clear filters</button>
            </div>
        @else
            <div class="projects-empty">
                <span><i class="ri-folder-add-line" aria-hidden="true"></i></span>
                <h2>Create your first project</h2>
                <p>Bring tasks, teammates, progress, and time together in one workspace.</p>
                <button type="button" data-bs-toggle="modal" data-bs-target="#projectModal" class="btn btn-primary"><i class="ri-add-line" aria-hidden="true"></i> New project</button>
            </div>
        @endif
    </section>
</div>

<div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div><h5 class="modal-title" id="projectModalLabel">Create a project</h5><p class="text-muted mb-0 mt-1 fs-12">Set up the work and invite the people who will move it forward.</p></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ url('new-project') }}" onsubmit="show();" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="projectName" class="form-label">Project name</label>
                        <input type="text" class="form-control" name="name" id="projectName" placeholder="Example: Client website redesign" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="projectDescription" class="form-label">Description <small class="text-muted fw-normal">Optional</small></label>
                        <textarea class="form-control" name="description" id="projectDescription" rows="3" placeholder="What outcome should this project deliver?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="parentProject" class="form-label">Parent project <small class="text-muted fw-normal">Optional</small></label>
                        <select class="form-select select2" name="parent_id" id="parentProject"><option value="">No parent project</option>@foreach($projects as $parentProject)<option value="{{ $parentProject->id }}">{{ $parentProject->parent ? $parentProject->parent->name.' > ' : '' }}{{ $parentProject->name }}</option>@endforeach</select>
                    </div>
                    <div class="mb-3">
                        <label for="projectTeamMembers" class="form-label">Team members</label>
                        <select class="form-control select2" name="team_member[]" multiple id="projectTeamMembers" required>@foreach($users as $user)<option value="{{ $user->id }}" @if($user->id == auth()->id()) selected @endif>{{ $user->name }}</option>@endforeach</select>
                        <small class="form-text">Only members from your team group are available.</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6"><label for="projectStatus" class="form-label">Status</label><select class="form-select" id="projectStatus" name="status" required><option value="To be started">To be started</option><option value="In Progress">In progress</option><option value="On Hold">On hold</option></select></div>
                        <div class="col-sm-6"><label for="projectImage" class="form-label">Project icon <small class="text-muted fw-normal">Optional</small></label><input type="file" class="form-control" name="icon" id="projectImage" accept="image/png,image/jpeg,image/webp"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="ri-check-line me-1" aria-hidden="true"></i> Create project</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('projectsContainer');
    if (!container) return;

    const cards = Array.from(container.querySelectorAll('.project-card'));
    const search = document.getElementById('projectSearch');
    const status = document.getElementById('projectStatusFilter');
    const sort = document.getElementById('projectSort');
    const noResults = document.getElementById('projectNoResults');
    const visibleCount = document.getElementById('visibleProjectCount');

    function updateProjects() {
        const query = search.value.trim().toLowerCase();
        const selectedStatus = status.value;
        let shown = 0;

        cards.forEach(function (card) {
            const matchesSearch = !query || card.dataset.search.indexOf(query) !== -1;
            const matchesStatus = selectedStatus === 'all' || card.dataset.status === selectedStatus;
            const visible = matchesSearch && matchesStatus;
            card.hidden = !visible;
            if (visible) shown++;
        });

        cards.sort(function (a, b) {
            if (sort.value === 'name') return a.dataset.name.localeCompare(b.dataset.name);
            if (sort.value === 'progress') return Number(b.dataset.progress) - Number(a.dataset.progress);
            return Number(b.dataset.updated) - Number(a.dataset.updated);
        }).forEach(function (card) { container.appendChild(card); });

        visibleCount.textContent = shown;
        noResults.hidden = shown !== 0;
        container.hidden = shown === 0;
    }

    search.addEventListener('input', updateProjects);
    status.addEventListener('change', updateProjects);
    sort.addEventListener('change', updateProjects);
    document.getElementById('clearProjectFilters').addEventListener('click', function () {
        search.value = '';
        status.value = 'all';
        sort.value = 'updated';
        updateProjects();
        search.focus();
    });
    updateProjects();
});

$(function () {
    $('#projectModal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({ dropdownParent: $('#projectModal'), width: '100%' });
    });
});
</script>
@endsection
