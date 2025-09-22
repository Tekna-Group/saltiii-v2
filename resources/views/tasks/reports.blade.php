@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<form action="{{ route('task.reports') }}" method="GET">
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="date_from" class="form-label">Date From</label>
            <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label">Date To</label>
            <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>
</form>
@if(request('date_from'))
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Task Report (Grouped by Project)</h5>
            </div>
            <div class="card-body">

                {{-- ===============================
                    Search Bar
                ================================ --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="taskSearch" class="form-control" placeholder="Search tasks by title, user, or ID...">
                    </div>
                </div>

                {{-- Filter Info --}}
                @if(request('date_from') || request('date_to'))
                    <div class="alert alert-info">
                        Showing tasks 
                        @if(request('date_from')) from <strong>{{ request('date_from') }}</strong> @endif
                        @if(request('date_to')) to <strong>{{ request('date_to') }}</strong> @endif
                    </div>
                @endif

                {{-- ===============================
                    Previous Backlogs Section
                ================================ --}}
                @if($previousBacklogs->count() > 0)
                    <div class="mb-5">
                        <h5 class="bg-danger text-white p-2 rounded">
                            Previous Backlogs
                            <span class="badge bg-light text-dark">
                                {{ $previousBacklogs->flatten()->count() }} Tasks
                            </span>
                        </h5>

                        @foreach($previousBacklogs as $projectName => $tasks)
                            <div class="mb-3">
                                <h6 class="bg-light p-2 rounded">
                                    {{ $projectName }}
                                    <span class="badge bg-secondary">{{ $tasks->count() }} Tasks</span>
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle task-table">
                                        <thead class="table-danger">
                                            <tr>
                                                <th>ID</th>
                                                <th>Task</th>
                                                <th>Assigned To</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tasks as $task)
                                                <tr class="table-warning">
                                                    <td>#{{ $task->id }}</td>
                                                    <td>{{ $task->title }}</td>
                                                    <td>
                                                        @foreach($task->users as $member)
                                                            <div class="avatar-group">
                                                                <span class="avatar-group-item" data-bs-toggle="tooltip" title="{{ $member->name }}">
                                                                </span>
                                                                {{ $member->name }}
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $task->due_date ? date('d M, Y', strtotime($task->due_date)) : 'No Due Date' }}</td>
                                                    <td>
                                                        <span class="badge bg-danger">{{ $task->board->board ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge 
                                                            @if($task->priority == 'High') bg-danger
                                                            @elseif($task->priority == 'Medium') bg-warning
                                                            @else bg-success @endif">
                                                            {{ $task->priority }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- ===============================
                    Main Report Grouped by Project
                ================================ --}}
                @forelse($tasksByProject as $projectName => $projectTasks)
                    <div class="mb-4">
                        <h5 class="bg-light p-2 rounded">
                            {{ $projectName }}
                            <span class="badge bg-secondary">{{ $projectTasks->count() }} Tasks</span>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle task-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Task</th>
                                        <th>Assigned To</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projectTasks as $task)
                                        <tr @if($task->due_date < date('Y-m-d') && $task->completed == 0) class="table-warning" @endif>
                                            <td>#{{ $task->id }}</td>
                                            <td>{{ $task->title }}</td>
                                            <td>
                                                @foreach($task->users as $member)
                                                    <div class="avatar-group">
                                                        <span class="avatar-group-item" data-bs-toggle="tooltip" title="{{ $member->name }}">
                                                            <img src="{{ $member->avatar ?? url('images/Favicon.png') }}" 
                                                                 class="rounded-circle avatar-xxs" 
                                                                 onerror="this.src='{{ url('images/Favicon.png') }}';">
                                                        </span>
                                                        {{ $member->name }}
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>{{ $task->due_date ? date('d M, Y', strtotime($task->due_date)) : 'No Due Date' }}</td>
                                            <td>
                                                @if($task->completed == 1)
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($task->due_date < date('Y-m-d'))
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($task->priority == 'High') bg-danger
                                                    @elseif($task->priority == 'Medium') bg-warning
                                                    @else bg-success @endif">
                                                    {{ $task->priority }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-secondary text-center">
                        No tasks found for the selected date range.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif
{{-- ===============================
    JavaScript for Search
=============================== --}}




   
@endsection
@section('js')
<script>
    document.getElementById('taskSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let tables = document.querySelectorAll('.task-table tbody');

        tables.forEach(tbody => {
            let rows = tbody.querySelectorAll('tr');

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });
</script>
<!-- list.js min js -->
<script src="{{asset('inside_css/assets/libs/list.js/list.min.js')}}"></script>

<!--list pagination js-->
<script src="{{asset('inside_css/assets/libs/list.pagination.js/list.pagination.min.js')}}"></script>

<!-- titcket init js -->
<script src="{{asset('inside_css/assets/js/pages/tasks-list.init.js')}}"></script>

<!-- Sweet Alerts js -->
<script src="{{asset('inside_css/assets/libs/list.js/list.min.js')}}"></script>

<!-- App js -->
<script src="{{asset('inside_css/assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
    
    // Initialize Select2 inside modals
    $('#showModal').on('shown.bs.modal', function () {
        $('.select2').select2({
            dropdownParent: $('#showModal')
        });
    });
  

   
});
</script>
@endsection
