@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
 <div class="row">
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
                                            <img src="{{asset($project->icon)}}" alt="" class="avatar-xs">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div>
                                        <h4 class="fw-bold">{{$project->name}}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            {{-- <div><i class="ri-building-line align-bottom me-1"></i> Themesbrand</div> --}}
                                            {{-- <div class="vr"></div> --}}
                                            <div>Create Date : <span class="fw-medium">{{date('d M, Y',strtotime($project->created_at))}}</span></div>
                                            <div class="vr"></div>
                                            <div>Last Update : <span class="fw-medium">{{date('d M, Y',strtotime($project->updated_at))}}</span></div>
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
<div class="row">
    <div class="col-lg-12">
        <div class="tab-content text-muted">
            <div class="tab-pane fade show active" id="tasks-overview" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-lg-auto">
                                <div class="hstack gap-2">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createboardModal"><i class="ri-add-line align-bottom me-1"></i> Create Board</button>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3 col-auto">
                                <div class="search-box">
                                    <input type="text" class="form-control search" id="search-task-options" placeholder="Search for tasks....">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-auto ms-sm-auto">
                                <div class="avatar-group" id="newMembar">
                                    @foreach($project->users as $member)
                                    <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                        <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xs">
                                    </a>
                                    @endforeach
                                    <a href="#addmemberModal" data-bs-toggle="modal" class="avatar-group-item material-shadow">
                                        <div class="avatar-xs">
                                            <div class="avatar-title rounded-circle">
                                                +
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end card-body-->
                </div>
                   <div class="tasks-board mb-3 " id="kanbanboard">
                    @foreach($project->boards as $board)
                        <div class="tasks-list bg-light p-2" style='background-color:gray !important;border-radius: 5px;'>
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="fs-14 text-uppercase fw-semibold mb-0 ml-2 " style='color:white;'>{{$board->board}} <small class="badge bg-success align-bottom ms-1 totaltask-badge">2</small></h6>
                                </div>
                                
                            </div>
                            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                                <div id="unassigned-task" class="tasks">
                                    <div class="card tasks-box">
                                        <div class="card-body">
                                            <div class="d-flex mb-2">
 												<h6 class="fs-15 mb-0 flex-grow-1 text-truncate task-title"><a href="apps-tasks-details.html" class="d-block">Profile Page Structure</a></h6>
                                                <div class="dropdown">
                                                    <a href="javascript:void(0);" class="text-muted" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill"></i></a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                                                        <li><a class="dropdown-item" href="apps-tasks-details.html"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="ri-edit-2-line align-bottom me-2 text-muted"></i> Edit</a></li>
                                                        <li><a class="dropdown-item" data-bs-toggle="modal" href="#deleteRecordModal"><i class="ri-delete-bin-5-line align-bottom me-2 text-muted"></i> Delete</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p class="text-muted">Profile Page means a web page accessible to the public or to guests.</p>
                                            <div class="mb-3">
                                                <div class="d-flex mb-1">
                                                    <div class="flex-grow-1">
                                                        <h6 class="text-muted mb-0"><span class="text-secondary">15%</span> of 100%</h6>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <span class="text-muted">03 Jan, 2022</span>
                                                    </div>
                                                </div>
                                                <div class="progress rounded-3 progress-sm">
                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <span class="badge bg-primary-subtle text-primary">Admin</span>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-group">
                                                        <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Alexis">
                                                            <img src="assets/images/users/avatar-6.jpg" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                        <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Nancy">
                                                            <img src="assets/images/users/avatar-5.jpg" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer border-top-dashed">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <h6 class="text-muted mb-0">#VL2436</h6>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <ul class="link-inline mb-0">
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void(0)" class="text-muted"><i class="ri-eye-line align-bottom"></i> 04</a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void(0)" class="text-muted"><i class="ri-question-answer-line align-bottom"></i> 19</a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void(0)" class="text-muted"><i class="ri-attachment-2 align-bottom"></i> 02</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                    <!--end card-->
                                    <div class="card tasks-box">
                                        <div class="card-body">
                                            <div class="d-flex mb-2">
                                                <div class="flex-grow-1">
                                               <h6 class="fs-15 mb-0 text-truncate task-title"><a href="apps-tasks-details.html" class="d-block">Velzon - Admin Layout Design</a></h6>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a href="javascript:void(0);" class="text-muted" id="dropdownMenuLink12" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill"></i></a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink12">
                                                        <li><a class="dropdown-item" href="apps-tasks-details.html"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="ri-edit-2-line align-bottom me-2 text-muted"></i> Edit</a></li>
                                                        <li><a class="dropdown-item" data-bs-toggle="modal" href="#deleteRecordModal"><i class="ri-delete-bin-5-line align-bottom me-2 text-muted"></i> Delete</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p class="text-muted">The dashboard is the front page of the Administration UI.</p>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <span class="badge bg-primary-subtle text-primary">Layout</span>
                                                    <span class="badge bg-primary-subtle text-primary">Admin</span>
                                                    <span class="badge bg-primary-subtle text-primary">Dashboard</span>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-group">
                                                        <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Michael">
                                                            <img src="assets/images/users/avatar-7.jpg" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                        <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Alexis">
                                                            <img src="assets/images/users/avatar-6.jpg" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                        <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anna">
                                                            <img src="assets/images/users/avatar-1.jpg" alt="" class="rounded-circle avatar-xxs">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end card-body-->
                                        <div class="card-footer border-top-dashed">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <span class="text-muted"><i class="ri-time-line align-bottom"></i> 07 Jan, 2022</span>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <ul class="link-inline mb-0">
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void(0)" class="text-muted"><i class="ri-eye-line align-bottom"></i> 14</a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void(0)" class="text-muted"><i class="ri-question-answer-line align-bottom"></i> 32</a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void(0)" class="text-muted"><i class="ri-attachment-2 align-bottom"></i> 05</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end card-->
                                </div>
                                <!--end tasks-->
                            </div>
                            <div class="my-3">
                                <button class="btn btn-soft-info w-100" data-bs-toggle="modal" data-bs-target="#creatertaskModal">Add More</button>
                            </div>
                        </div>
                    @endforeach
                        <!--end tasks-list-->
                    </div>
            </div>
        </div>
    </div>
</div>
@include('projects.new-board')
@include('projects.add_member')
@include('projects.add_task')
@endsection
@section('js')
<script src="{{asset('inside_css/assets/js/pages/project-list.init.js')}}"></script>

<!-- Select2 JS -->

 <!-- dragula init js -->

<script src="{{asset('inside_css/assets/libs/dragula/dragula.min.js')}}"></script>

<!-- dom autoscroll -->
<script src="{{asset('inside_css/assets/libs/dom-autoscroller/dom-autoscroller.min.js')}}"></script>

<!--taks-kanban-->
<script src="{{asset('inside_css/assets/js/pages/tasks-kanban.init.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    
    // Initialize Select2 inside modals
    $('#addmemberModal').on('shown.bs.modal', function () {
        $('.select2').select2({
            dropdownParent: $('#addmemberModal')
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
@endsection
