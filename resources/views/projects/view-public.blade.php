<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="referrer" content="no-referrer">
    <title>{{ $project->name }} - Read-only project board</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .kanban-board-container {
            display: flex;
            overflow-x: auto;
            padding: 15px;
        }

        .kanban-board-wrapper {
            display: flex;
            gap: 16px;
            min-height: 50vh;
        }

        .kanban-column {
            background: #ffffff;
            border-radius: 8px;
            min-width: 300px;
            border: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
        }

        .kanban-header {
            padding: 12px;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
            background: #f1f3f5;
        }

        .kanban-items {
            padding: 12px;
            max-height: 420px;
            overflow-y: auto;
        }

        .kanban-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .kanban-card h6 {
            font-size: 14px;
            margin-bottom: 6px;
        }

        .task-meta {
            font-size: 12px;
            color: #6c757d;
        }

        .read-only-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
            padding: 8px 11px;
            border-radius: 999px;
            color: #176f59;
            background: #e5f5ef;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .public-notice {
            margin: -8px 0 16px;
            color: #6c757d;
            font-size: 12px;
        }

        .load-all-tasks {
            padding: 0 12px 12px;
        }

        .load-all-tasks button {
            width: 100%;
            border-style: dashed;
            font-size: 12px;
        }

        @media (max-width: 576px) {
            .project-header-content {
                align-items: flex-start !important;
                flex-wrap: wrap;
            }

            .read-only-badge {
                margin-left: 64px;
            }
        }
    </style>
    <link href="{{ asset('inside_css/assets/css/saltiii-public-project.css') }}" rel="stylesheet">
</head>

<body>

<main class="container-fluid public-project-page">

    <!-- Project Header -->
    <div class="card public-project-header">
        <div class="card-body d-flex align-items-center project-header-content">
            <img src="{{ asset($project->icon) }}"
                 onerror="this.src='{{ url('images/Favicon.png') }}'"
                 class="public-project-icon"
                 width="50" alt="">

            <div class="public-project-copy">
                <span class="public-eyebrow">Shared project board</span>
                <h1>{{ $project->name }}</h1>
                @if($project->description)<p>{{ $project->description }}</p>@endif
                <div class="public-project-dates">
                    <span><i class="bi bi-calendar3"></i> Created {{ date('M j, Y', strtotime($project->created_at)) }}</span>
                    <span><i class="bi bi-arrow-repeat"></i> Updated {{ $project->updated_at->diffForHumans() }}</span>
                </div>
            </div>
            <span class="read-only-badge"><i class="bi bi-eye"></i> Read-only shared view</span>
        </div>
    </div>
    <p class="public-notice"><i class="bi bi-shield-check me-1"></i>This page is for viewing only. Changes require an authorized SALTIII account.</p>

    <section class="public-project-summary" aria-label="Project summary">
        <div><span><i class="bi bi-list-check"></i></span><p><small>Total tasks</small><strong>{{ $publicTaskCount }}</strong></p></div>
        <div><span><i class="bi bi-hourglass-split"></i></span><p><small>Open</small><strong>{{ $publicOpenCount }}</strong></p></div>
        <div><span><i class="bi bi-check2-circle"></i></span><p><small>Completed</small><strong>{{ $publicCompletedCount }}</strong></p></div>
        <div class="{{ $publicOverdueCount ? 'has-attention' : '' }}"><span><i class="bi bi-graph-up-arrow"></i></span><p><small>Progress</small><strong>{{ $publicProgress }}%</strong><em>{{ $publicOverdueCount }} overdue</em></p></div>
    </section>

    <section class="public-board-toolbar" aria-labelledby="public-board-title">
        <div>
            <span class="public-eyebrow">Workflow</span>
            <h2 id="public-board-title">Tasks by status <span id="publicVisibleTaskCount">{{ $publicTaskCount }}</span></h2>
        </div>
        <div class="public-board-tools">
            <div class="public-scroll-controls" aria-label="Scroll board">
                <button type="button" id="publicScrollLeft" aria-label="Scroll board left" title="Scroll left"><i class="bi bi-arrow-left"></i></button>
                <button type="button" id="publicScrollRight" aria-label="Scroll board right" title="Scroll right"><i class="bi bi-arrow-right"></i></button>
            </div>
            <label class="public-search" for="publicTaskSearch">
                <i class="bi bi-search"></i>
                <input type="search" id="publicTaskSearch" placeholder="Search tasks" autocomplete="off">
                <span class="visually-hidden">Search tasks</span>
            </label>
        </div>
    </section>

    <!-- Kanban Board -->
    <div class="kanban-board-container" id="publicBoard">
        <div class="kanban-board-wrapper">

            @forelse($boardData as $board)
                <section class="kanban-column" data-public-column="{{ $board['id'] }}">

                    <div class="kanban-header"><span class="public-status-title"><i></i>{{ $board['name'] }}</span><span class="public-status-count">{{ $board['total'] }}</span></div>

                    <div class="kanban-items" id="public-board-items-{{ $board['id'] }}">

                        @forelse($board['tasks'] as $task)
                            <article class="kanban-card public-task-card" data-search="{{ strtolower($task['name']) }}">

                                <div class="public-task-heading"><h3>@if((int) $task['completed'] === 1)<i class="bi bi-check-circle-fill text-success me-1"></i>@endif{{ $task['name'] }}</h3><span>#{{ $task['id'] }}</span></div>

                                <div class="public-task-meta">
                                    <span><i class="bi bi-calendar-event"></i>{{ $task['due_date'] ?: 'No due date' }}</span>
                                    <span class="public-priority public-priority-{{ strtolower($task['priority'] ?: 'low') }}">{{ $task['priority'] ?: 'Low' }}</span>
                                </div>

                                <footer class="public-task-footer"><span><i class="bi bi-chat"></i>{{ $task['comments'] }}</span><span><i class="bi bi-paperclip"></i>{{ $task['attachments'] }}</span><span><i class="bi bi-clock"></i>{{ number_format($task['hours'], 1) }}h</span></footer>
                            </article>
                        @empty
                            <div class="public-column-empty">No tasks in this status</div>
                        @endforelse

                    </div>
                    @if($board['total'] > 10)
                        <div class="load-all-tasks" id="public-board-loader-{{ $board['id'] }}">
                            <button type="button" class="btn btn-outline-primary" data-url="{{ route('public.project.board.tasks', [$project->public_share_token, $board['id']]) }}" onclick="loadPublicBoard(this, '{{ $board['id'] }}')">
                                <i class="bi bi-chevron-down me-1"></i> Show all {{ $board['total'] }} tasks
                            </button>
                        </div>
                    @endif
                </section>
            @empty
                <div class="public-board-empty"><i class="bi bi-kanban"></i><strong>No statuses yet</strong><span>This shared project does not have a board to display.</span></div>
            @endforelse

        </div>
    </div>

</main>

<script>
    async function loadPublicBoard(button, boardId) {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Loading tasks...';

        try {
            const response = await fetch(button.dataset.url, {headers: {'Accept': 'application/json'}});
            if (!response.ok) throw new Error('Unable to load tasks');

            const data = await response.json();
            const container = document.getElementById('public-board-items-' + boardId);
            container.innerHTML = data.tasks.length
                ? data.tasks.map(renderPublicTask).join('')
                : '<div class="public-column-empty">No tasks in this status</div>';
            document.getElementById('public-board-loader-' + boardId).remove();
            filterPublicTasks(document.getElementById('publicTaskSearch').value.trim().toLowerCase());
            return true;
        } catch (error) {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Try loading tasks again';
            return false;
        }
    }

    function renderPublicTask(task) {
        const priority = task.priority || 'Low';
        const priorityClass = priority.toLowerCase().replace(/[^a-z0-9-]/g, '');
        const completedIcon = Number(task.completed) === 1 ? '<i class="bi bi-check-circle-fill text-success me-1"></i>' : '';
        return '<article class="kanban-card public-task-card" data-search="' + escapePublicHtml(String(task.name).toLowerCase()) + '">' +
            '<div class="public-task-heading"><h3>' + completedIcon + escapePublicHtml(task.name) + '</h3><span>#' + Number(task.id) + '</span></div>' +
            '<div class="public-task-meta"><span><i class="bi bi-calendar-event"></i>' + escapePublicHtml(task.due_date || 'No due date') + '</span><span class="public-priority public-priority-' + priorityClass + '">' + escapePublicHtml(priority) + '</span></div>' +
            '<footer class="public-task-footer"><span><i class="bi bi-chat"></i>' + Number(task.comments) + '</span><span><i class="bi bi-paperclip"></i>' + Number(task.attachments) + '</span><span><i class="bi bi-clock"></i>' + Number(task.hours).toFixed(1) + 'h</span></footer>' +
            '</article>';
    }

    function escapePublicHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function filterPublicTasks(query) {
        let visible = 0;
        document.querySelectorAll('[data-public-column]').forEach(function (column) {
            let columnVisible = 0;
            column.querySelectorAll('.public-column-empty:not(.public-search-empty)').forEach(function (emptyState) {
                emptyState.hidden = Boolean(query);
            });
            column.querySelectorAll('.public-task-card').forEach(function (card) {
                const matches = !query || card.dataset.search.indexOf(query) !== -1;
                card.hidden = !matches;
                if (matches) {
                    visible++;
                    columnVisible++;
                }
            });

            let empty = column.querySelector('.public-search-empty');
            if (query && columnVisible === 0) {
                if (!empty) {
                    empty = document.createElement('div');
                    empty.className = 'public-column-empty public-search-empty';
                    empty.textContent = 'No matching tasks';
                    column.querySelector('.kanban-items').appendChild(empty);
                }
                empty.hidden = false;
            } else if (empty) {
                empty.hidden = true;
            }
        });

        document.getElementById('publicVisibleTaskCount').textContent = query ? visible : {{ $publicTaskCount }};
    }

    const publicSearch = document.getElementById('publicTaskSearch');
    let publicSearchTimer = null;
    publicSearch.addEventListener('input', function () {
        clearTimeout(publicSearchTimer);
        publicSearchTimer = setTimeout(async function () {
            const query = publicSearch.value.trim().toLowerCase();
            if (query) {
                const loaders = Array.from(document.querySelectorAll('.load-all-tasks button'));
                await Promise.all(loaders.map(function (button) {
                    const boardId = button.closest('[data-public-column]').dataset.publicColumn;
                    return loadPublicBoard(button, boardId);
                }));
            }
            filterPublicTasks(query);
        }, 250);
    });

    const publicBoard = document.getElementById('publicBoard');
    const scrollLeftButton = document.getElementById('publicScrollLeft');
    const scrollRightButton = document.getElementById('publicScrollRight');
    function updatePublicScrollButtons() {
        const maximum = Math.max(0, publicBoard.scrollWidth - publicBoard.clientWidth);
        scrollLeftButton.disabled = publicBoard.scrollLeft <= 2;
        scrollRightButton.disabled = publicBoard.scrollLeft >= maximum - 2;
    }
    scrollLeftButton.addEventListener('click', function () { publicBoard.scrollBy({left: -420, behavior: 'smooth'}); });
    scrollRightButton.addEventListener('click', function () { publicBoard.scrollBy({left: 420, behavior: 'smooth'}); });
    publicBoard.addEventListener('scroll', updatePublicScrollButtons, {passive: true});
    window.addEventListener('resize', updatePublicScrollButtons);
    updatePublicScrollButtons();
</script>
</body>
</html>
