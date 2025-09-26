@extends('layouts.header')

@section('css')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endsection

@section('content')
<form action="{{ route('task.reports') }}" method="GET">
    <div class="row mb-4">
        <!-- Date Filters -->
        <div class="col-md-2">
            <label for="date_from" class="form-label">Date From</label>
            <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label">Date To</label>
            <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>

        <!-- User Filter -->
        <div class="col-md-3">
            <label for="user_id" class="form-label">Filter by User</label>
            <select name="user_id" id="user_id" class="form-control select2">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Project Filter -->
        <div class="col-md-3">
            <label for="project_id" class="form-label">Filter by Project</label>
            <select name="project_id" id="project_id" class="form-control select2">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Submit Button -->
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>
</form>

@if(request()->anyFilled(['date_from', 'date_to', 'user_id', 'project_id']))
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title">Task Reports</h5>
            </div>
            <div class="card-body">

                {{-- Filter Info --}}
                <div class="alert alert-info">
                    Showing tasks
                    @if(request('date_from')) from <strong>{{ request('date_from') }}</strong> @endif
                    @if(request('date_to')) to <strong>{{ request('date_to') }}</strong> @endif
                    @if(request('user_id')) | User: <strong>{{ $users->where('id', request('user_id'))->first()->name }}</strong> @endif
                    @if(request('project_id')) | Project: <strong>{{ $projects->where('id', request('project_id'))->first()->name }}</strong> @endif
                </div>

                {{-- ===============================
                    TASK PROGRESS REPORT
                ================================ --}}
                <h5 class="bg-primary text-white p-2 rounded">Task Progress Report</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover" id="taskProgressTable" data-title="Task Progress Report">
                        <thead class="table-primary">
                            <tr>
                                <th>Task ID</th>
                                <th>Task Name</th>
                                <th>Assigned To</th>
                                <th>Project</th>
                                <th>Due Date</th>
                                <th>Days Due</th>
                                <th>Status</th>
                                <th>Priority</th>
                            </tr>
                            <tr class="filters">
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Task ID" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Task Name" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Assigned To" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Project" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Due Date" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Days Due" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Status" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Priority" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasksProgress as $task)
                                <tr>
                                    <td>#{{ $task->id }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->assignedUserNames() }}</td>
                                    <td>{{ $task->project->name ?? 'N/A' }}</td>
                                    <td>{{ $task->due_date ? date('d M, Y', strtotime($task->due_date)) : 'No Due Date' }}</td>
                                    <td>
                                        @if($task->due_date && $task->completed == 0 && now()->gt($task->due_date))
                                            {{ now()->diffInDays($task->due_date) }}
                                        @else
                                            0
                                        @endif
                                    </td>
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

                {{-- ===============================
                    TASK AGING REPORT
                ================================ --}}
                <h5 class="bg-warning text-dark p-2 rounded">Task Aging Report</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover" id="taskAgingTable" data-title="Task Aging Report">
                        <thead class="table-warning">
                            <tr>
                                <th>Task ID</th>
                                <th>Task Name</th>
                                <th>Assigned To</th>
                                <th>Project</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                            </tr>
                            <tr class="filters">
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Task ID" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Task Name" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Assigned To" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Project" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Due Date" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Days Overdue" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasksAging as $task)
                                <tr>
                                    <td>#{{ $task->id }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->assignedUserNames() }}</td>
                                    <td>{{ $task->project->name ?? 'N/A' }}</td>
                                    <td>{{ date('d M, Y', strtotime($task->due_date)) }}</td>
                                    <td>{{ now()->diffInDays($task->due_date) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===============================
                    COMPLETED TASKS REPORT
                ================================ --}}
                <h5 class="bg-success text-white p-2 rounded">Completed Tasks Report</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="taskCompletedTable" data-title="Completed Tasks Report">
                        <thead class="table-success">
                            <tr>
                                <th>Task ID</th>
                                <th>Task Name</th>
                                <th>Assigned To</th>
                                <th>Project</th>
                                <th>Completed On</th>
                            </tr>
                            <tr class="filters">
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Task ID" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Task Name" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Assigned To" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Project" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Completed On" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasksCompleted as $task)
                                <tr>
                                    <td>#{{ $task->id }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->assignedUserNames() }}</td>
                                    <td>{{ $task->project->name ?? 'N/A' }}</td>
                                    <td>{{ date('d M, Y', strtotime($task->updated_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables Scripts -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();

    function initDataTable(tableId) {
        let table = $(tableId).DataTable({
            paging: false,        // Show all rows
            ordering: false,      // Disable sorting
            info: false,          // Hide "Showing X of Y"
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: $(tableId).attr('data-title'),
                    text: 'Export to Excel',
                    className: 'btn btn-success mb-3'
                }
            ]
        });

        // Apply column filters
        $(tableId + ' thead tr.filters th').each(function(index) {
            $('input', this).on('keyup change clear', function() {
                if (table.column(index).search() !== this.value) {
                    table.column(index).search(this.value).draw();
                }
            });
        });
    }

    // Initialize all three tables
    initDataTable('#taskProgressTable');
    initDataTable('#taskAgingTable');
    initDataTable('#taskCompletedTable');
});
</script>
@endsection
