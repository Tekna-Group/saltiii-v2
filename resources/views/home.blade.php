@extends('layouts.header')
@section('css')
    <link href="{{asset('inside_css/assets/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{url('assets/libs/swiper/swiper-bundle.min.css')}}">
@endsection
@section('content')

<div class="row project-wrapper">
    <div class="col-xxl-9">
        <div class="row">
            <div class="col-xl-4">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <a href='#'>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-2 fs-2">
                                    <i data-feather="briefcase" class="text-primary"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden ms-3">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Active Projects</p>
                                <div class="d-flex align-items-center mb-3">
                                    <a href='#' href="#" data-bs-target="#projects"  data-bs-toggle="modal"><h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{$projects->where('completed',0)->count()}}">0</span></h4></a>
                                    {{-- <span class="badge bg-danger-subtle text-danger fs-12"><i class="ri-arrow-down-s-line fs-13 align-middle me-1"></i>5.02 %</span> --}}
                                </div>
                                <p class="text-muted text-truncate mb-0">Projects</p>
                            </div>
                        
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-4">
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
                                    <a href='#' href="#" data-bs-target="#delayed_tasks"  data-bs-toggle="modal"><h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{($tasks->where('due_date','<',date('Y-m-d')))->count()}}">0</span></h4></a>
                                    {{-- <span class="badge bg-success-subtle text-success fs-12"><i class="ri-arrow-up-s-line fs-13 align-middle me-1"></i>3.58 %</span> --}}
                                </div>
                                <p class="text-muted mb-0">Need action</p>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div><!-- end col -->

            <div class="col-xl-4">
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
                                    <a href='#' href="#" data-bs-target="#ongoing_tasks"  data-bs-toggle="modal"><h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{($tasks)->count()}}">0</span></h4></a>
                                    {{-- <span class="badge bg-danger-subtle text-danger fs-12"><i class="ri-arrow-down-s-line fs-13 align-middle me-1"></i>10.35 %</span> --}}
                                </div>
                                <p class="text-muted text-truncate mb-0">Tasks this week</p>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div>
            <!-- end col -->
        </div><!-- end row -->
        <div class='row'>
            <div class='col-xl-12'>
                <div class="card">
                    <div class="card-body">
                        <h5 class="">Projects  </h5>
                        <!-- Swiper -->
                       <div class="swiper project-swiper">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="w-50">
                                    <input type="text" id="projectSearch" class="form-control" placeholder="Search projects...">
                                </div>
                                <div class="d-flex gap-2">
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
                            </div>

                            <div class="swiper-wrapper">
                                @foreach($projects as $project)
                                    <div class="swiper-slide project-slide">
                                        <a href="#">
                                            <div class="card profile-project-card shadow-none profile-project-success mb-0 material-shadow">
                                                <div class="card-body p-4">
                                                    <!-- Project Header -->
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1 text-muted overflow-hidden">
                                                            <h5 class="fs-14 text-truncate mb-1 project-name">
                                                                
                                                                
                                                                
                                                                <a href="#" class="text-body">{{ $project->name }}</a> 
                                                                    
                                                            </h5>
                                                                <small><span class='fs-14 text-muted'> <i class="ri-calendar-event-fill"></i> Last Update: 
                                                                @if($project->tasks->isNotEmpty())
                                                                    {{ ($project->latest_comment_updated_at )
                                                                        ? (date('M d, Y',strtotime($project->latest_comment_updated_at)))
                                                                        : 'No Action yet' }}
                                                                @else
                                                                    No Action yet
                                                                @endif
                                                                </span></small>
                                                        </div>
                                                        <div class="flex-shrink-0 ms-2 text-end">
                                                            <div class="badge bg-warning-subtle text-warning fs-10">
                                                                {{ $project->status }}
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Footer Section -->
                                                <div class="card-footer bg-light border-top p-2">
                                                    <div class="d-flex text-muted">
                                                        <ul class="list-inline mb-0 d-flex align-items-center gap-2 w-100">
                                                            <li class="list-inline-item">
                                                                <i class="ri-timer-fill"></i> {{number_format($project->activities->sum('hours'),2)}} hrs
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <i class="ri-question-answer-line"></i> {{$project->comments->count()}}
                                                            </li>
                                                            <li class="list-inline-item">
                                                                <i class="ri-attachment-2"></i> {{$project->attachments->count()}}
                                                            </li>

                                                            <!-- Last Updated Icon + Date -->
                                                        

                                                            <!-- Completed Tasks Count -->
                                                            <li class="list-inline-item ms-auto">
                                                                <div class="flex-shrink-0">
                                                                    <i class="ri-list-check align-bottom me-1 text-muted"></i>
                                                                    {{ $project->tasks->where('completed', 1)->count() }}/{{ $project->tasks->count() }}
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <!-- end card body -->
                </div><!-- end card -->
            </div>
        </div>
        <div class='row g-3'>
             @php
                $sections = [
                    'Delayed' => ['tasks' => $tasks->where('due_date', '<', date('Y-m-d')), 'color' => 'danger'],
                    'Due Today' => ['tasks' => $tasks->where('due_date', date('Y-m-d')), 'color' => 'warning'],
                    'Not Yet Delayed' => ['tasks' => $tasks->where('due_date', '>', date('Y-m-d')), 'color' => 'success'],
                ];
                $key = 0;
            @endphp
             @foreach($sections as $sectionTitle => $sectionData)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1 fw-bold text-{{$sectionData['color']}}">
                                {{$sectionTitle}} 
                                <span class="badge bg-{{$sectionData['color']}}">{{ $sectionData['tasks']->count() }}</span>
                            </h4>
                            <div class="flex-shrink-0 ms-3">
                                <input type="text" 
                                    class="form-control form-control-sm" 
                                    placeholder="Search tasks..." 
                                    id="searchTasks{{$key}}">
                            </div>
                        </div><!-- end card header -->
                         <div class="tasks-scroll px-2" style="height:500px; overflow-y:auto;" id="tasksContainer{{$key}}">
                            @forelse($sectionData['tasks'] as $task)
                                <a href="#" data-bs-toggle="offcanvas" data-bs-target="#taskDetails{{$task->id}}" id="taskCard{{$task->id}}" class="text-decoration-none task-item">
                                    <div class="card-body p-1 mt-1" title="{{ $task->title }}" style=' cursor: pointer;'>
                                        <div class="card profile-project-card shadow-none mb-0 profile-project-{{$sectionData['color']}} material-shadow">
                                            <div class="card-body p-2">
                                                <!-- Project Header -->
                                                <div class="d-flex">
                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                        <h5 class="fs-14 text-truncate mb-1 project-name">
                                                            <a href="#" class="text-body">{{ strlen($task->title) > 20 ? substr($task->title, 0, 20) . '…' : $task->title }}</a>
                                                        </h5> 
                                                        <p class="text-muted mb-0"><span class="fs-7">{{ strlen($task->project->name) > 20 ? substr($task->project->name, 0, 20) . '…' : $task->project->name }}</span> - <span class='badge bg-success-subtle text-dark fs-10'>{{$task->board->board}}</span> <br>
                                                        <small class='fs-8 text-muted'>
                                                            <i class="ri-calendar-event-fill"></i> 
                                                                @if($task->due_date)
                                                                    {{ ($task->due_date )
                                                                        ? (date('M d, Y',strtotime($task->due_date)))
                                                                        : 'No Due Date' }}
                                                                @else
                                                                    No Due Date
                                                                @endif
                                                                </small>
                                                        </p>
                                                    
                                                    </div>
                                                    <div class="flex-shrink-0 ms-2 text-end">
                                                        <div class="badge  @if($task->priority == 'High') text-white" style="background-color:#FF8A80;" 
                                                            @elseif($task->priority == 'Medium') text-dark" style="background-color:#FFD180;" 
                                                            @else text-white" style="background-color:#B9F6CA;" 
                                                            @endif" fs-10">
                                                              {{$task->priority}}
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Footer Section -->
                                            <div class="card-footer bg-light border-top p-2">
                                                <div class="d-flex text-muted">
                                                    <ul class="list-inline mb-0 d-flex align-items-center gap-2 w-100">
                                                        <li class="list-inline-item">
                                                            <i class="ri-timer-fill"></i> 0 hrs
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <i class="ri-question-answer-line"></i> 0
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <i class="ri-attachment-2"></i> 0
                                                        </li>
                                                        <li class="list-inline-item ms-auto">
                                                            <div class="flex-shrink-0">
                                                                <i class="ri-list-check align-bottom me-1 text-muted"></i>
                                                                0
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <hr>
                                </a>
                            @empty
                                <div class="text-center text-muted py-3">No tasks</div>
                            @endforelse
                         </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-xl-4">
                <div class="card card-height">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1 py-1">My Pending Tasks</h4>
                        
                    </div><!-- end card header -->
                    <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="today" role="tabpanel">
                                <div class="profile-timeline">
                                    <div class="accordion accordion-flush" id="todayExample">
                                        @foreach(
                                            $tasks->sortBy('due_date')
                                                  ->filter(fn($task) => $task->users->contains('id', auth()->id()) && $task->completed == 0)
                                            as $task
                                        )
                                            <div class="accordion-item border-0">
                                                <div class="accordion-header" id="headingThree">
                                                    <a class="accordion-button p-2 shadow-none" href="{{url('view-project/view-task/'.$task->id)}}" target='_blank' >
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <img src="{{ asset($task->project->icon) }}" 
                                                                     onerror="this.src='{{url('images/Favicon.png')}}';" 
                                                                     title='{{$task->title}}'  
                                                                     alt="" 
                                                                     class="avatar-xs rounded-circle material-shadow" />
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">{{substr($task->title,0,20)}} - <i @if($task->due_date < date('Y-m-d')) class='text-danger' @endif>{{date('M d, Y',strtotime($task->due_date))}}</i></h6>
                                                                <small class="text-muted mb-2">
                                                                    {{$task->project->name}}
                                                                    
                                                                </small>
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
                {{-- <div class="card card-height">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Total Hours Spend</h4>
                      
                    </div><!-- end cardheader -->
                    <div class="card-body">
                        <div id="portfolio_donut_charts" data-projects='@json($projects_data)'
                        data-total-hours='{{ $totalHours }}' data-colors='["--vz-primary", "--vz-info", "--vz-warning", "--vz-success"]' data-colors-minimal='["--vz-primary", "--vz-primary-rgb, 0.85", "--vz-primary-rgb, 0.65", "--vz-primary-rgb, 0.50"]' data-colors-interactive='["--vz-primary", "--vz-primary-rgb, 0.85", "--vz-primary-rgb, 0.65", "--vz-primary-rgb, 0.50"]' data-colors-corporate='["--vz-primary", "--vz-secondary", "--vz-info", "--vz-success"]' data-colors-galaxy='["--vz-primary", "--vz-primary-rgb, 0.85", "--vz-primary-rgb, 0.65", "--vz-primary-rgb, 0.50"]' class="apex-charts" dir="ltr"></div>

                        <ul class="list-group list-group-flush border-dashed mb-0 mt-3 pt-2">
                            @foreach($projects_data as $proj)
                            <li class="list-group-item px-0">
                                <div class="d-flex">
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-1">{{$proj['title']}}</h6>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1 text-success">{{$proj['hours']}} Hr/s</h6>
                                    </div>
                                </div>
                            </li><!-- end -->
                            @endforeach
                          
                        </ul><!-- end -->
                    </div><!-- end card body -->
                </div><!-- end card --> --}}
               
            </div><!-- end col -->
            <div class="col-xl-8">
               
              
                @if(auth()->user()->role == "Admin")
                <div class="card card-height">
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
                                        <th scope="col">Pending Tasks</th>
                                        <th scope="col">Delayed Tasks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                    <tr>
                                        <td><a href="{{url('/view-profile/'.$member->id)}}" >{{$member->name}}</a></td>
                                        <td>{{$member->activities->sum('hours')}} hrs</td>
                                        <td><a href='#' data-bs-target="#ongoing_tasks{{$member->id}}"  data-bs-toggle="modal">{{$member->tasks->where('completed',0)->count()}}</a></td>
                                        <td><a href='#' data-bs-target="#delayed_tasks{{$member->id}}"  data-bs-toggle="modal">{{($member->tasks)->where('completed',0)->where('due_date','<',date('Y-m-d'))->count()}}</a></td>
                                    </tr>
                                    @endforeach
                                </tbody><!-- end tbody -->
                            </table><!-- end table -->
                        </div>
                    </div><!-- end cardbody -->
                </div><!-- end card -->
                @endif
            </div>
        </div>
    </div>
    <div class="col-xxl-3">
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-height">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1 py-1">Timesheet</h4>
                        
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-bordered table-nowrap table-centered align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Date</th>
                                        <th scope="col">Hours</th>
                                    </tr>
                                </thead><!-- end thead -->
                               <tbody>
                                @php
                                    $tot_hours = 0;
                                @endphp
                                    @for ($date = $last_sunday; $date <= $saturday; $date = date('Y-m-d', strtotime($date . ' +1 day')))
                                        @php
                                        $tot_hours = $tot_hours + $activities->where('user_id',auth()->user()->id)->where('date',date('Y-m-d', strtotime($date)))->sum('hours') ;
                                        @endphp
                                            <tr>
                                                    <td >{{ date('M d - l',strtotime($date)) }}</td>
                                                <td>{{ $activities->where('user_id',auth()->user()->id)->where('date',date('Y-m-d', strtotime($date)))->sum('hours') }} hrs</td>
                                            </tr>
                                    @endfor
                                    <tr>
                                        <td >Total</td>
                                        <td>{{$tot_hours}} hrs</td>
                                    </tr>
                                </tbody>
                            </table><!-- end table -->
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 me-2">Recent Activity</h4>
                        <div class="flex-shrink-0 ms-auto"></div>
                    </div>
                    <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="today" role="tabpanel">
                                <div class="profile-timeline">
                                    <div class="accordion accordion-flush" id="todayExample">
                                        @foreach($activities->sortByDesc('date') as $activity)
                                            <div class="accordion-item border-0">
                                                <div class="accordion-header" id="headingThree">
                                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapsethree" aria-expanded="false">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <img src="{{asset($activity->user->avatar)}}" 
                                                                     onerror="this.src='{{url('images/Favicon.png')}}';" 
                                                                     title='{{$activity->user->name}}'  
                                                                     alt="" 
                                                                     class="avatar-xs rounded-circle material-shadow" />
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="fs-14 mb-1">{{substr($activity->activity,0,20)}} - {{$activity->hours}} hr/s</h6>
                                                                <small class="text-muted mb-2">
                                                                    {{$activity->project->name}} - 
                                                                    <span class="text-secondary">{{$activity->task->title}}</span>  
                                                                    <i>{{date('M d, Y',strtotime($activity->date))}}</i>
                                                                </small>
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
            <div class='col-lg-12'>
                 <div class="card card-h-100">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="modalBody"></div>
      </div>
    </div>
  </div>
@include('home.view_projects')
@foreach($members as $member)
    @include('home.view_employee_tasks')
    @include('home.view_employee_delayed')
@endforeach
@endsection
@section('js')
<script src="{{asset('inside_css/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

<!-- Swiper Js -->
<script src="{{asset('inside_css/assets/libs/swiper/swiper-bundle.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const modalEl = document.getElementById('eventModal');
      const modalBody = document.getElementById('modalBody');
      const modal = new bootstrap.Modal(modalEl);
    
      // Convert $tasks from Laravel → JS
      const tasks = @json($tasks);
    
      const today = new Date();
      const events = tasks.map(task => {
        const due = new Date(task.due_date);
        return {
          title: task.title,
          start: task.due_date,
          allDay: true,
          extendedProps: {
            description: task.description,
            users: task.users.map(u => u.name) // assuming Task->users has 'name'
          },
          color: due <= today ? "red" : "" // red if due today or overdue
        };
      });
    
      const calendar = new FullCalendar.Calendar(calendarEl, {
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,dayGridWeek,dayGridDay'
  },
  events: events,
  dayMaxEventRows: true,   // enable "more" link
  views: {
    dayGridMonth: { dayMaxEventRows: 3 } // show only 3 per day
  },
  eventClick: function(info) {
    const e = info.event;
    modalBody.innerHTML = `
      <p><strong>Title:</strong> ${e.title}</p>
      <p><strong>Description:</strong> ${e.extendedProps.description}</p>
      <p><strong>Due Date:</strong> ${e.start.toLocaleDateString()}</p>
      <p><strong>Participants:</strong> ${e.extendedProps.users.join(", ")}</p>
    `;
    modal.show();
  }
});
    
      calendar.render();
    });
    </script>

<!-- CRM js -->
<!-- ApexCharts -->
<script>
    function getChartColorsArray(id) {
        const el = document.getElementById(id);
        if (!el) return;
    
        let colors = el.getAttribute("data-colors");
        if (!colors) {
            console.warn("data-colors not found for", id);
            return;
        }
    
        try {
            colors = JSON.parse(colors).map(function (value) {
                let color = value.replace(" ", "");
                if (color.indexOf(",") === -1) {
                    return getComputedStyle(document.documentElement).getPropertyValue(color) || color;
                } else {
                    const parts = value.split(",");
                    if (parts.length === 2) {
                        return "rgba(" +
                            getComputedStyle(document.documentElement).getPropertyValue(parts[0]).trim() +
                            "," + parts[1] + ")";
                    }
                    return color;
                }
            });
        } catch (e) {
            console.error("Invalid JSON for", id, e);
        }
    
        return colors;
    }
    
    document.addEventListener("DOMContentLoaded", function () {
        const el = document.getElementById("portfolio_donut_charts");
        const projects = JSON.parse(el.dataset.projects);
        const totalHours = parseFloat(el.dataset.totalHours);
        const labels = projects.map(p => p.title);
        const series = projects.map(p => parseFloat(Number(p.hours).toFixed(2)));
        const chartColors = getChartColorsArray("portfolio_donut_charts");
    
        const options = {
            series: series,
            labels: labels,
            chart: {
                type: "donut",
                height: 224
            },
            plotOptions: {
                pie: {
                    size: 100,
                    offsetX: 0,
                    offsetY: 0,
                    donut: {
                        size: "70%",
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: "18px",
                                offsetY: -5
                            },
                            value: {
                                show: true,
                                fontSize: "20px",
                                color: "#343a40",
                                fontWeight: 500,
                                offsetY: 5
                            },
                            total: {
                                show: true,
                                fontSize: "13px",
                                label: "Total Hours",
                                color: "#9599ad",
                                fontWeight: 500,
                                formatter: function () {
                                    return totalHours;
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { show: false },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            stroke: { lineCap: "round", width: 2 },
            colors: chartColors
        };
    
        const chart = new ApexCharts(el, options);
        chart.render();
    });
</script>
<script src="{{url('assets/libs/swiper/swiper-bundle.min.js')}}"></script>
<script src="{{url('assets/js/pages/profile.init.js')}}"></script>
<script>
    // Initialize Swiper
    const projectSwiper = new Swiper('.project-swiper', {
        slidesPerView: 3,
        spaceBetween: 20,
        navigation: {
            nextEl: '.slider-button-next',
            prevEl: '.slider-button-prev',
        },
    });

    // Search Functionality
    document.getElementById('projectSearch').addEventListener('keyup', function() {
        let searchValue = this.value.toLowerCase();
        let slides = document.querySelectorAll('.swiper-wrapper .project-slide');

        slides.forEach(function(slide) {
            let projectName = slide.querySelector('.project-name').textContent.toLowerCase();
            if (projectName.includes(searchValue)) {
                slide.classList.remove('d-none'); // Show
            } else {
                slide.classList.add('d-none');    // Hide
            }
        });

        // Update Swiper after hiding/showing slides
        projectSwiper.update();
    });
</script>
@endsection
