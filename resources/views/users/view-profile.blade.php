@extends('layouts.header')
@section('css')
<link rel="stylesheet" href="{{url('assets/libs/swiper/swiper-bundle.min.css')}}">

@endsection
@section('content')
<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        <img src="assets/images/profile-bg.jpg" alt="" class="profile-wid-img" />
    </div>
</div>
 <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
    <div class="row g-4">
        <div class="col-auto">
            <div class="avatar-lg">
                <img src="{{asset($user->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';"  alt="user-img" class="img-thumbnail rounded-circle" />
            </div>
        </div>
        <!--end col-->
        <div class="col">
            <div class="p-2">
                <h3 class="text-white mb-1">{{$user->name}}</h3>
                <p class="text-white text-opacity-75">{{$user->title}}</p>
                <div class="hstack text-white-50 gap-1">
                    <div class="me-2"><i class="ri-mail-line me-1 text-white text-opacity-75 fs-16 align-middle"></i> {{$user->email}}</div>
                    <div>
                        <i class="ri-time-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{number_format($user->activities->sum('hours'),2)}} Total Hours
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
        <!--end col-->

    </div>
    <!--end row-->
</div>
     <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex profile-wrapper">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Overview</span>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#activities" role="tab">
                                <i class="ri-list-unordered d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Activities</span>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#projects" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Projects</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#documents" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Documents</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-xxl-3">

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Info</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Full Name :</th>
                                                        <td class="text-muted">{{$user->name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">E-mail :</th>
                                                        <td class="text-muted">{{$user->name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Creation Date</th>
                                                        <td class="text-muted">{{date('M d, Y',strtotime($user->created_at))}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Due Tasks</h5>
                                            </div>
                                        </div>
                                        @foreach($tasks->where('completed',0)->where('due_date','<',date('Y-m-d')) as $task)
                                            <div class="d-flex mb-4">
                                            
                                                {{-- <div class="flex-shrink-0">
                                                    <img src="assets/images/small/img-4.jpg" alt="" height="50" class="rounded material-shadow" />
                                                </div> --}}
                                                <div class="flex-grow-1 ms-3 overflow-hidden">
                                                    <a href="{{url('view-project/view-task/'.$task->id)}}">
                                                        <h6 class="text-truncate fs-14 text-danger">{{$task->project->name}} - {{$task->title}}</h6>
                                                    </a>
                                                    <p class="text-muted mb-0">{{date('M d, Y',strtotime($task->due_date))}}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <!--end card-body-->
                                </div>
                                <!--end card-->
                            </div>
                            <!--end col-->
                            <div class="col-xxl-9">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Pending Tasks <br></h5>
                                        <!-- Swiper -->
                                        <div class="swiper project-swiper ">
                                            <div class="d-flex justify-content-end gap-2 mb-2">
                                                <div class="slider-button-prev">
                                                    <div class="avatar-title fs-18 rounded px-1 material-shadow">
                                                        <i class="ri-arrow-left-s-line"></i>
                                                    </div>
                                                </div>
                                                <div class="slider-button-next">
                                                    <div class="avatar-title fs-18 rounded px-1 material-shadow">
                                                        <i class="ri-arrow-right-s-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swiper-wrapper">
                                                @foreach($tasks->where('completed',0)->sortBy('due_date') as $task)
                                                <div class="swiper-slide">
                                                    <div class="card profile-project-card shadow-none @if($task->due_date < date('Y-m-d')) profile-project-danger  @else profile-project-success @endif mb-0 material-shadow">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex">
                                                                <div class="flex-grow-1 text-muted overflow-hidden">
                                                                    <h5 class="fs-14 text-truncate mb-1">
                                                                        <a href="#" class="text-body">{{$task->project->name}}</a>
                                                                    </h5>
                                                                    <p class="text-muted text-truncate mb-0"> {{$task->title}}</p>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    <div class="badge bg-warning-subtle text-warning fs-10"> {{$task->board->board}}</div> <br>
                                                                    <div class="badge text-white-subtle text-danger fs-10"> {{date('M d, Y',strtotime($task->due_date))}}</div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex mt-4">
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div>
                                                                            <h5 class="fs-12 text-muted mb-0"> Members :</h5>
                                                                        </div>
                                                                        <div class="avatar-group">
                                                                            @foreach($task->users as $member)
                                                                                <div class="avatar-group-item material-shadow">
                                                                                    <div class="avatar-xs">
                                                                                        <img src="{{asset($member->avatar)}}" title="{{$member->name}}" onerror="this.src='{{url('images/Favicon.png')}}';"  alt="" class="rounded-circle avatar-xs" />
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end card body -->
                                                    </div>
                                                    <!-- end card -->
                                                </div>
                                                @endforeach
                                                <!-- end slide item -->
                                            </div>

                                        </div>

                                    </div>
                                    <!-- end card body -->
                                </div><!-- end card -->

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0  me-2">Recent Activity</h4>
                                                <div class="flex-shrink-0 ms-auto">
                                                    {{-- <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab" href="#today" role="tab">
                                                                Today
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab" href="#weekly" role="tab">
                                                                Weekly
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab" href="#monthly" role="tab">
                                                                Monthly
                                                            </a>
                                                        </li>
                                                    </ul> --}}
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="tab-content text-muted">
                                                    <div class="tab-pane active" id="today" role="tabpanel">
                                                        <div class="profile-timeline">
                                                            <div class="accordion accordion-flush" id="todayExample">
                                                                @foreach($user->activities->sortByDesc('id') as $activity)
                                                                    <div class="accordion-item border-0">
                                                                        <div class="accordion-header" id="headingThree">
                                                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapsethree" aria-expanded="false">
                                                                                <div class="d-flex">
                                                                                    <div class="flex-shrink-0">
                                                                                        <img src="{{asset($user->avatar)}}"  alt="" class="avatar-xs rounded-circle material-shadow" />
                                                                                    </div>
                                                                                    <div class="flex-grow-1 ms-3">
                                                                                        <h6 class="fs-14 mb-1"> {{$activity->activity}} - {{$activity->hours}} hrs</h6>
                                                                                        <small class="text-muted mb-2">{{$activity->project->name}} -<span class="text-secondary">{{$activity->task->title}}</span> submitted -{{date('M d, Y h:i a',strtotime($activity->created_at))}}</small>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <!--end accordion-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane fade" id="projects" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    @foreach($projects as $project)
                                        <div class="col-xxl-3 col-sm-6">
                                            <div class="card profile-project-card shadow-none profile-project-warning material-shadow">
                                                <div class="card-body p-4">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1 text-muted overflow-hidden">
                                                            <h5 class="fs-14 text-truncate"><a href="{{url('view-project/'.$project->id)}}" class="text-body">{{$project->name}}</a></h5>
                                                            <p class="text-muted text-truncate mb-0">Last Update : <span class="fw-semibold text-body">{{date('M d, Y',strtotime($project->updated_at))}}</span></p>
                                                        </div>
                                                        <div class="flex-shrink-0 ms-2">
                                                            <div class="badge bg-warning-subtle text-warning fs-10">{{$project->status}}</div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-4">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div>
                                                                    <h5 class="fs-12 text-muted mb-0">Members :</h5>
                                                                </div>
                                                                <div class="avatar-group">
                                                                     @foreach($project->users as $member)
                                                                        <div class="avatar-group-item material-shadow">
                                                                            <div class="avatar-xs">
                                                                                <img src="{{asset($member->avatar)}}" title="{{$member->name}}" onerror="this.src='{{url('images/Favicon.png')}}';"  alt="" class="rounded-circle avatar-xs" />
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end card body -->
                                            </div>
                                            <!-- end card -->
                                        </div>
                                    @endforeach
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <h5 class="card-title flex-grow-1 mb-0">Documents</h5>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle mb-0">
                                                <thead class="table-light text-muted">
                                                    <tr>
                                                        <th scope="col">File Name</th>
                                                        <th scope="col">Type</th>
                                                        <th scope="col">Size</th>
                                                        <th scope="col">Upload Date</th>
                                                        <th scope="col">Uploaded by</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   @foreach($tasks as $task)
                                                        @foreach($task->attachments as $attachment)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm">
                                                                    <div class="avatar-title bg-info-subtle text-info rounded fs-20">
                                                                        <i class="ri-folder-line"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-3 flex-grow-1">
                                                                    <h6 class="fs-15 mb-0"><a href="{{url($attachment->file)}}" target='_blank'>{{$attachment->name}}</a></h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{$attachment->file_type}} File</td>
                                                        <td>{{$attachment->file_size}} MB</td>
                                                        <td>{{date('d M, Y',strtotime($attachment->created_at))}}</td>
                                                        <td>{{$attachment->user->name}}</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <a href="javascript:void(0);" class="btn btn-light btn-icon" id="dropdownMenuLink3" data-bs-toggle="dropdown" aria-expanded="true">
                                                                    <i class="ri-equalizer-fill"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink3" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 23px);">
                                                                    <li><a class="dropdown-item" href="{{url($attachment->file)}}" target='_blank'><i class="ri-eye-fill me-2 align-middle"></i>View</a></li>
                                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-5-line me-2 align-middle"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="javascript:void(0);" class="text-success"><i class="mdi mdi-loading mdi-spin fs-20 align-middle me-2"></i> Load more </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end tab-pane-->
                </div>
                <!--end tab-content-->
            </div>
        </div>
        <!--end col-->
    </div>
@endsection
@section('js')
<script src="{{url('assets/libs/swiper/swiper-bundle.min.js')}}"></script>
<script src="{{url('assets/js/pages/profile.init.js')}}"></script>
<script src="{{url('assets/js/app.js')}}"></script>
@endsection
