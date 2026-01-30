<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $project->name }} – Public Board</title>

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
    </style>
</head>

<body>

<div class="container-fluid mt-4">

    <!-- Project Header -->
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center">
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
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board-container">
        <div class="kanban-board-wrapper">

            @foreach($boardData as $board)
                <div class="kanban-column">

                    <div class="kanban-header">
                        {{ $board['name'] }}
                        <span class="badge bg-secondary float-end">
                            {{ count($board['tasks']) }}
                        </span>
                    </div>

                    <div class="kanban-items">

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
                </div>
            @endforeach

        </div>
    </div>

</div>

</body>
</html>
