@extends('layouts.header')
@section('css')
@endsection
@section('content')

<div class="row g-4 mb-3">
    <div class="col-sm-auto">
        <div>
            <a href="{{url('new-project')}}" class="btn btn-soft-secondary"><i class="ri-add-line align-bottom me-1"></i> Add New</a>
        </div>
    </div>
    <div class="col-sm">
        <div class="d-flex justify-content-sm-end gap-2">
            <div class="search-box ms-2">
                <input type="text" class="form-control" placeholder="Search...">
                <i class="ri-search-line search-icon"></i>
            </div>

            <select class="form-control w-md" data-choices data-choices-search-false>
                <option value="All">All</option>
                <option value="Today">Today</option>
                <option value="Yesterday" selected>Yesterday</option>
                <option value="Last 7 Days">Last 7 Days</option>
                <option value="Last 30 Days">Last 30 Days</option>
                <option value="This Month">This Month</option>
                <option value="Last Year">Last Year</option>
            </select>
        </div>
    </div>
</div><!-- end row -->  
<div class="row">
    <div class="col-xxl-3 col-sm-6 project-card">
        <div class="card">
            <div class="card-body">
                <div class="p-3 mt-n3 mx-n3 bg-secondary-subtle rounded-top">
                    <div class="d-flex gap-1 align-items-center justify-content-end my-n2">
                        <button type="button" class="btn avatar-xs p-0 favourite-btn active">
                            <span class="avatar-title bg-transparent fs-15">
                                <i class="ri-star-fill"></i>
                            </span>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-1 mt-n1 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i data-feather="more-horizontal" class="icon-sm"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{url('view-project')}}"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                    View</a>
                                <a class="dropdown-item" href="{{url('view-project')}}"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                    Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                    Remove</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center pb-3">
                        <img src="assets/images/brands/dribbble.png" alt="" height="32">
                    </div>
                </div>

                <div class="py-3">
                    <h5 class="fs-14 mb-3"><a href="{{url('view-project')}}" class="text-body">Kanban Board</a></h5>
                    <div class="row gy-3">
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Status</p>
                                <div class="badge bg-warning-subtle text-warning fs-12">Inprogess</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Deadline</p>
                                <h5 class="fs-14">08 Dec, 2021</h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <p class="text-muted mb-0 me-2">Team :</p>
                        <div class="avatar-group">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Terry Moberly">
                                <div class="avatar-xxs">
                                    <div class="avatar-title rounded-circle bg-success">
                                        T
                                    </div>
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ruby Miller">
                                <div class="avatar-xxs">
                                    <img src="assets/images/users/avatar-5.jpg" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Add Members">
                                <div class="avatar-xxs">
                                    <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                        +
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1">
                            <div>Tasks</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div><i class="ri-list-check align-bottom me-1 text-muted"></i> 17/20
                            </div>
                        </div>
                    </div>
                    <div class="progress progress-sm animated-progress">
                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="71" aria-valuemin="0" aria-valuemax="100" style="width: 71%;">
                        </div><!-- /.progress-bar -->
                    </div><!-- /.progress -->
                </div>

            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-3 col-sm-6 project-card">
        <div class="card">
            <div class="card-body">
                <div class="p-3 mt-n3 mx-n3 bg-light rounded-top">
                    <div class="d-flex gap-1 align-items-center justify-content-end my-n2">
                        <button type="button" class="btn avatar-xs p-0 favourite-btn">
                            <span class="avatar-title bg-transparent fs-15">
                                <i class="ri-star-fill"></i>
                            </span>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-1 mt-n1 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i data-feather="more-horizontal" class="icon-sm"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{url('view-project')}}"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                    View</a>
                                <a class="dropdown-item" href="apps-projects-create.html"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                    Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                    Remove</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center pb-3">
                        <img src="assets/images/brands/slack.png" alt="" height="32">
                    </div>
                </div>
                <div class="py-3">
                    <h5 class="mb-3 fs-14"><a href="{{url('view-project')}}" class="text-body">Ecommerce app</a></h5>
                    <div class="row gy-3">
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Status</p>
                                <div class="badge bg-warning-subtle text-warning fs-12">Inprogress</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Deadline</p>
                                <h5 class="fs-14">20 Nov, 2021</h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <p class="text-muted mb-0 me-2">Team :</p>
                        <div class="avatar-group">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Irma Metz">
                                <img src="assets/images/users/avatar-9.jpg" alt="" class="rounded-circle avatar-xxs">
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="James Clem">
                                <img src="assets/images/users/avatar-10.jpg" alt="" class="rounded-circle avatar-xxs">
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Add Members">
                                <div class="avatar-xxs">
                                    <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                        +
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1">
                            <div>Tasks</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div><i class="ri-list-check align-bottom me-1 text-muted"></i> 11/45
                            </div>
                        </div>
                    </div>
                    <div class="progress progress-sm animated-progress">
                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                        </div><!-- /.progress-bar -->
                    </div><!-- /.progress -->
                </div>

            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-xxl-3 col-sm-6 project-card">
        <div class="card">
            <div class="card-body">
                <div class="p-3 mt-n3 mx-n3 bg-primary-subtle rounded-top">
                    <div class="d-flex gap-1 align-items-center justify-content-end my-n2">
                        <button type="button" class="btn avatar-xs p-0 favourite-btn">
                            <span class="avatar-title bg-transparent fs-15">
                                <i class="ri-star-fill"></i>
                            </span>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-1 mt-n1 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i data-feather="more-horizontal" class="icon-sm"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{url('view-project')}}"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                    View</a>
                                <a class="dropdown-item" href="apps-projects-create.html"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                    Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                    Remove</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center pb-3">
                        <img src="assets/images/brands/dropbox.png" alt="" height="32">
                    </div>
                </div>

                <div class="py-3">
                    <h5 class="mb-3 fs-14"><a href="{{url('view-project')}}" class="text-body">Redesign - Landing page</a></h5>
                    <div class="row gy-3">
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Status</p>
                                <div class="badge bg-warning-subtle text-warning fs-12">Inprogress</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Deadline</p>
                                <h5 class="fs-14">10 Jul, 2021</h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <p class="text-muted mb-0 me-2">Team :</p>
                        <div class="avatar-group">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                <div class="avatar-xxs">
                                    <img src="assets/images/users/avatar-3.jpg" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Sylvia Wright">
                                <div class="avatar-xxs">
                                    <div class="avatar-title rounded-circle bg-primary">
                                        S
                                    </div>
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ellen Smith">
                                <div class="avatar-xxs">
                                    <img src="assets/images/users/avatar-4.jpg" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Add Members">
                                <div class="avatar-xxs">
                                    <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                        +
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1">
                            <div>Tasks</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div><i class="ri-list-check align-bottom me-1 text-muted"></i> 13/26
                            </div>
                        </div>
                    </div>
                    <div class="progress progress-sm animated-progress">
                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="54" aria-valuemin="0" aria-valuemax="100" style="width: 54%;">
                        </div><!-- /.progress-bar -->
                    </div><!-- /.progress -->
                </div>

            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xxl-3 col-sm-6 project-card">
        <div class="card">
            <div class="card-body">
                <div class="p-3 mt-n3 mx-n3 bg-danger-subtle rounded-top">
                    <div class="d-flex gap-1 align-items-center justify-content-end my-n2">
                        <button type="button" class="btn avatar-xs p-0 favourite-btn active">
                            <span class="avatar-title bg-transparent fs-15">
                                <i class="ri-star-fill"></i>
                            </span>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-1 mt-n1 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i data-feather="more-horizontal" class="icon-sm"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{url('view-project')}}"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                    View</a>
                                <a class="dropdown-item" href="apps-projects-create.html"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                    Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#removeProjectModal"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                    Remove</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center pb-3">
                        <img src="assets/images/brands/mail_chimp.png" alt="" height="32">
                    </div>
                </div>

                <div class="py-3">
                    <h5 class="mb-3 fs-14"><a href="{{url('view-project')}}" class="text-body">Multipurpose landing template</a></h5>
                    <div class="row gy-3">
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Status</p>
                                <div class="badge bg-success-subtle text-success fs-12">Completed</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <p class="text-muted mb-1">Deadline</p>
                                <h5 class="fs-14">18 Sep, 2021</h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <p class="text-muted mb-0 me-2">Team :</p>
                        <div class="avatar-group">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Donna Kline">
                                <div class="avatar-xxs">
                                    <div class="avatar-title rounded-circle bg-success">
                                        D
                                    </div>
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Lee Winton">
                                <div class="avatar-xxs">
                                    <img src="assets/images/users/avatar-5.jpg" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Johnny Shorter">
                                <div class="avatar-xxs">
                                    <img src="assets/images/users/avatar-6.jpg" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Add Members">
                                <div class="avatar-xxs">
                                    <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                        +
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1">
                            <div>Tasks</div>
                        </div>
                        <div class="flex-shrink-0">
                            <div><i class="ri-list-check align-bottom me-1 text-muted"></i> 25/32
                            </div>
                        </div>
                    </div>
                    <div class="progress progress-sm animated-progress">
                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;">
                        </div><!-- /.progress-bar -->
                    </div><!-- /.progress -->
                </div>

            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
@section('js')
<script src="{{asset('inside_css/assets/js/pages/project-list.init.js')}}"></script>
@endsection
