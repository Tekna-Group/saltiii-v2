@extends('layouts.header')
@section('css')
@endsection
@section('content')

<div class="row project-wrapper">
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-2 fs-2">
                                    <i data-feather="briefcase" class="text-primary"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Active Projects</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{$projects->where('completed',0)->count()}}">0</span></h4>
                                    {{-- <span class="badge bg-danger-subtle text-danger fs-12"><i class="ri-arrow-down-s-line fs-13 align-middle me-1"></i>5.02 %</span> --}}
                                </div>
                                <p class="text-muted text-truncate mb-0">Projects</p>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-3">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-2 fs-2">
                                    <i data-feather="award" class="text-danger"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-3">Delayed Tasks</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{($tasks->where('due_date','<',date('Y-m-d')))->count()}}">0</span></h4>
                                    {{-- <span class="badge bg-success-subtle text-success fs-12"><i class="ri-arrow-up-s-line fs-13 align-middle me-1"></i>3.58 %</span> --}}
                                </div>
                                <p class="text-muted mb-0">Need action</p>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-3">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded-2 fs-2">
                                    <i data-feather="calendar" class="text-warning"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">On-going Tasks</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{($tasks)->count()}}">0</span></h4>
                                    {{-- <span class="badge bg-danger-subtle text-danger fs-12"><i class="ri-arrow-down-s-line fs-13 align-middle me-1"></i>10.35 %</span> --}}
                                </div>
                                <p class="text-muted text-truncate mb-0">Tasks this week</p>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded-2 fs-2">
                                    <i data-feather="clock" class="text-info"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Total Hours</p>
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{$activities->sum('hours')}}">0</span>h</h4>
                                    {{-- <span class="badge bg-danger-subtle text-danger fs-12"><i class="ri-arrow-down-s-line fs-13 align-middle me-1"></i>10.35 %</span> --}}
                                </div>
                                <p class="text-muted text-truncate mb-0">({{date('M d',strtotime($last_sunday))}} - {{date('M d',strtotime($saturday))}}) </p>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div>
            <!-- end col -->
        </div><!-- end row -->
    </div>
</div><!-- end row -->
<div class="row">
    

    <div class="col-xl-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 py-1">My Tasks</h4>
                
            </div><!-- end card header -->
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-nowrap table-centered align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Task</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Status</th>
                                <th scope="col">Assignee</th>
                            </tr>
                        </thead><!-- end thead -->
                        <tbody>
                            @foreach($tasks as $task)
                            <tr @if($task->due_date < date('Y-m-d')) class='text-danger' @endif>
                                <td>{{$task->title}}</td>
                                <td>{{date('M d',strtotime($task->due_date))}}</td>
                                <td>{{$task->board->board}}</td>
                                <td>
                                    @foreach($task->users as $member)
                                    @if($member->avatar)
                                    <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                        <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xs">
                                    </a>
                                    @else
                                     <div class="avatar-circle bg-primary text-white fw-bold text-center rounded-circle" style="width:40px; height:40px; line-height:40px;">
                                        {{ strtoupper(substr($member->avatar, 0, 3)) }}
                                    </div>
                                    @endif
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    <div class="col-xl-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 py-1">My Activities</h4>
                
            </div><!-- end card header -->
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-bordered table-nowrap table-centered align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Project/Task</th>
                                <th scope="col">Activity</th>
                                <th scope="col">Hours</th>
                            </tr>
                        </thead><!-- end thead -->
                       <tbody>
                            @for ($date = $last_sunday; $date <= $saturday; $date = date('Y-m-d', strtotime($date . ' +1 day')))
                                @php
                                    $dayActivities = $activities->where('date', $date);
                                    $rowCount = $dayActivities->count();
                                @endphp

                                @if ($rowCount > 0)
                               @foreach ($dayActivities as $act)
                                    <tr>
                                        @if ($loop->first)
                                            <td rowspan="{{ $rowCount }}">{{ date('M d - l',strtotime($date)) }}</td>
                                        @endif
                                        <td>{{ $act->project->name }} - {{ $act->task->title }}</td>
                                        <td>{{ $act->activity }}</td>
                                        <td>{{ $act->hours }} hrs</td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td>{{ $date }}</td>
                                        <td colspan='3' class='text-center text-danger'>No activities</td>
                                    </tr>
                                @endif
                            @endfor
                            <tr>
                                <td colspan='3'>Total</td>
                                <td>{{$activities->sum('hours')}} hrs</td>
                            </tr>
                        </tbody>
                    </table><!-- end table -->
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->
@if(auth()->user()->role == "Admin")
    <div class="row">
        <div class="col-xl-4">
            <div class="card card-height-100">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title flex-grow-1 mb-0">Projects</h4>
                    <div class="flex-shrink-0">
                        <a href="javascript:void(0);" class="btn btn-soft-info btn-sm material-shadow-none">Export Report</a>
                    </div>
                </div><!-- end cardheader -->
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-nowrap table-centered align-middle">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th scope="col">Project Name</th>
                                    <th scope="col">Tasks</th>
                                    <th scope="col">Assignee</th>
                                    <th scope="col">Total Hours</th>
                                </tr><!-- end tr -->
                            </thead><!-- thead -->

                            <tbody>
                                @foreach($projects as $project)
                                <tr>
                                    <td>{{$project->name}}</td>
                                    <td>{{$project->tasks->where('completed',1)->count()."/".$project->tasks->count()}}</td>
                                    <td>

                                        @foreach($project->users as $member)
                                        @if($member->avatar)
                                            <a href="javascript: void(0);" class="avatar-group-item material-shadow" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$member->name}}">
                                                <img src="{{asset($member->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="" class="rounded-circle avatar-xs">
                                            </a>
                                        @else
                                            <div class="avatar-circle bg-primary text-white fw-bold text-center rounded-circle" style="width:40px; height:40px; line-height:40px;">
                                            {{ strtoupper(substr($member->name, 0, 3)) }}
                                        </div>
                                        @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        {{$project->activities->sum('hours')}} hrs
                                    </td>
                                </tr>
                                @endforeach
                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>


                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-xxl-4">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Team Members ({{date('M d',strtotime($last_sunday))}} - {{date('M d',strtotime($saturday))}})</h4>
                    <div class="flex-shrink-0">
                    </div>
                </div><!-- end card header -->

                <div class="card-body">

                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-nowrap align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">Member</th>
                                    <th scope="col">Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                <tr>
                                    <td>{{$member->name}}</td>
                                    <td>{{$member->activities->sum('hours')}} hrs</td>
                                </tr>
                                @endforeach
                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->


    </div>
@endif
@endsection
@section('js')
<script src="{{asset('inside_css/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

    <!-- Vector map-->

    <!-- Dashboard init -->
    <script src="{{asset('inside_css/assets/js/pages/dashboard-projects.init.js')}}"></script>
@endsection
