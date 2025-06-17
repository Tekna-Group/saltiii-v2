@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="row">
    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Total Tasks</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{$tasks->count()}}">0</span></h2>
                        {{-- <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0"> <i class="ri-arrow-up-line align-middle"></i> 17.32 %</span> vs. previous month</p> --}}
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-4">
                                <i class="ri-ticket-2-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div>
    <!--end col-->
    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Pending Tasks</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{$tasks->where('completed',0)->count()}}">0</span></h2>
                        {{-- <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0"> <i class="ri-arrow-down-line align-middle"></i> 0.87 %</span> vs. previous month</p> --}}
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-4">
                                <i class="mdi mdi-timer-sand"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div>
    <!--end col-->
    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Completed Tasks</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{$tasks->where('completed',1)->count()}}">0</span></h2>
                        {{-- <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0"> <i class="ri-arrow-down-line align-middle"></i> 2.52 % </span> vs. previous month</p> --}}
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-4">
                                <i class="ri-checkbox-circle-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div>
    <!--end col-->
    <div class="col-xxl-3 col-sm-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Archived Tasks</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{$tasks->where('archived',1)->count()}}">0</span></h2>
                        {{-- <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0"> <i class="ri-arrow-up-line align-middle"></i> 0.63 % </span> vs. previous month</p> --}}
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-4">
                                <i class="ri-delete-bin-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div>
    </div>
    <!--end col-->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">All Tasks</h5>
                  
                </div>
            </div>
            <!--end card-body-->
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                        <thead class="table-light text-muted">
                            <tr>
                               
                                <th class="sort" data-sort="id">ID</th>
                                <th class="sort" data-sort="project_name">Project</th>
                                <th class="sort" data-sort="tasks_name">Task</th>
                                <th class="sort" data-sort="assignedto">Assigned To</th>
                                <th class="sort" data-sort="due_date">Due Date</th>
                                <th class="sort" data-sort="status">Status</th>
                                <th class="sort" data-sort="priority">Priority</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all">
                            @foreach($tasks as $task)
                            <tr>
                                
                                <td class="id"><a href="{{url('view-project/view-task/'.$task->id)}}" target='_blank' class="fw-medium link-primary">#{{$task->id}}</a></td>
                                <td class="project_name"><a href="{{url('view-project/'.$task->project_id)}}" target='_blank' class="fw-medium link-primary">{{$task->project->name}}</a></td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{url('view-project/view-task/'.$task->id)}}" target='_blank' class="fw-medium link-primary"><div class="flex-grow-1 tasks_name">{{$task->title}}</div><a>
                                    </div>
                                </td>
                                <td class="assignedto">
                                    <div class="avatar-group">
                                        @foreach($task->users as $member)
                                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                            <img src="{{$member->avatar}}" onerror="this.src='{{url('images/Favicon.png')}}';"  alt="" class="rounded-circle avatar-xxs" />
                                        </a>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="due_date">{{date('d M, Y',strtotime($task->due_date))}}</td>
                                <td class="status"><span class="badge bg-secondary-subtle text-secondary text-uppercase">{{$task->board->board}}</span></td>
                                <td class="priority"><span class="badge bg-danger text-uppercase">{{$task->priority}}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!--end table-->
                    <div class="noresult" style="display: none">
                        <div class="text-center">
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 200k+ tasks We did not find any tasks for you search.</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <div class="pagination-wrap hstack gap-2">
                        <a class="page-item pagination-prev disabled" href="#">
                            Previous
                        </a>
                        <ul class="pagination listjs-pagination mb-0"></ul>
                        <a class="page-item pagination-next" href="#">
                            Next
                        </a>
                    </div>
                </div>
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@endsection
@section('js')
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
