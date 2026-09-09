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
</head>

<body>

<div class="container-fluid mt-4">

    <!-- Project Header -->
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center project-header-content">
            <img src="{{ asset($project->icon) }}"
                 onerror="this.src='{{ url('images/Favicon.png') }}'"
                 class="rounded-circle me-3"
                 width="50">

            <div>
                <h4 class="mb-1">{{ $project->name }}</h4>
                <small class="text-muted">
                    Created {{ date('d M Y', strtotime($project->created_at)) }} ·
                    Updated {{ date('d M Y', strtotime($project->updated_at)) }}
                </small>
            </div>
            <span class="read-only-badge"><i class="bi bi-eye"></i> Read-only shared view</span>
        </div>
    </div>
    <p class="public-notice"><i class="bi bi-shield-check me-1"></i>This page is for viewing only. Changes require an authorized SALTIII account.</p>

    <!-- Kanban Board -->
    <div class="kanban-board-container">
        <div class="kanban-board-wrapper">

            @foreach($boardData as $board)
                <div class="kanban-column">

                    <div class="kanban-header">
                        {{ $board['name'] }}
                        <span class="badge bg-secondary float-end">
                            {{ $board['total'] }}
                        </span>
                    </div>

                    <div class="kanban-items" id="public-board-items-{{ $board['id'] }}">

                        @forelse($board['tasks'] as $task)
                            <div class="kanban-card">

                                <h6>
                                    {{ \Illuminate\Support\Str::limit($task['name'], 40) }}
                                </h6>

                                <div class="task-meta mb-1">
                                    <i class="bi bi-hash"></i> {{ $task['id'] }}
                                </div>

                                <div class="task-meta">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ $task['due_date'] ?? 'No due date' }}
                                </div>

                                <div class="task-meta mt-2 d-flex justify-content-between">
                                    <span>
                                        <i class="bi bi-clock"></i> {{ number_format($task['hours'], 2) }}h
                                    </span>
                                    <span>
                                        <i class="bi bi-chat"></i> {{ $task['comments'] }}
                                        <i class="bi bi-paperclip ms-2"></i> {{ $task['attachments'] }}
                                    </span>
                                </div>

                            </div>
                        @empty
                            <p class="text-muted text-center mt-3">No tasks</p>
                        @endforelse

                    </div>
                    @if($board['total'] > 10)
                        <div class="load-all-tasks" id="public-board-loader-{{ $board['id'] }}">
                            <button type="button" class="btn btn-outline-primary" data-url="{{ route('public.project.board.tasks', [$project->public_share_token, $board['id']]) }}" onclick="loadPublicBoard(this, '{{ $board['id'] }}')">
                                <i class="bi bi-chevron-down me-1"></i> Show all {{ $board['total'] }} tasks
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach

        </div>
    </div>

</div>

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
                : '<p class="text-muted text-center mt-3">No tasks</p>';
            document.getElementById('public-board-loader-' + boardId).remove();
        } catch (error) {
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Try loading tasks again';
        }
    }

    function renderPublicTask(task) {
        return '<div class="kanban-card">' +
            '<h6>' + (Number(task.completed) === 1 ? '<i class="bi bi-check-circle-fill text-success me-1"></i>' : '') + escapePublicHtml(task.name) + '</h6>' +
            '<div class="task-meta mb-1"><i class="bi bi-hash"></i> ' + Number(task.id) + '</div>' +
            '<div class="task-meta"><i class="bi bi-calendar-event"></i> ' + escapePublicHtml(task.due_date || 'No due date') + '</div>' +
            '<div class="task-meta mt-2 d-flex justify-content-between"><span><i class="bi bi-clock"></i> ' + Number(task.hours).toFixed(2) + 'h</span>' +
            '<span><i class="bi bi-chat"></i> ' + Number(task.comments) + ' <i class="bi bi-paperclip ms-2"></i> ' + Number(task.attachments) + '</span></div>' +
            '</div>';
    }

    function escapePublicHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
</body>
</html>
