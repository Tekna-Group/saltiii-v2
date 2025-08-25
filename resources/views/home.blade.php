@extends('layouts.header')
@section('css')
    <link href="{{asset('inside_css/assets/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css" rel="stylesheet">
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
        <div class="row">
            <div class="col-xl-4">
                <div class="card card-height">
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
                </div><!-- end card -->
               
            </div><!-- end col -->
            <div class="col-xl-8">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Projects Hours</h4>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <canvas id="projectsBarChart"></canvas>
                    </div><!-- end card-body -->
                </div><!-- end card -->
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
                                        <td><a href="{{url('/view-profile/'.$member->id)}}" target="_blank">{{$member->name}}</a></td>
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

  // Sample events array
  const sampleEvents = [
    { id: '1', title: 'Team Standup', start: '2025-08-05T09:30:00', extendedProps: { location: 'Zoom', type: 'Meeting' } },
    { id: '2', title: 'Design Review', start: '2025-08-12T14:00:00', extendedProps: { location: 'Room A', type: 'Discussion' } },
    { id: '3', title: 'Payment Deadline', start: '2025-08-25', allDay: true, extendedProps: { location: 'Portal', type: 'Deadline' } }
  ];

  // Initialize FullCalendar
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,dayGridWeek,dayGridDay'
    },
    events: sampleEvents, // provide events directly as an array :contentReference[oaicite:3]{index=3}
    eventClick: function(info) {
      const e = info.event;
      modalBody.innerHTML = `
        <p><strong>Title:</strong> ${e.title}</p>
        <p><strong>Start:</strong> ${e.start.toLocaleString()}</p>
        ${e.extendedProps.location ? `<p><strong>Location:</strong> ${e.extendedProps.location}</p>` : ''}
        ${e.extendedProps.type ? `<p><strong>Type:</strong> ${e.extendedProps.type}</p>` : ''}
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
        const series = projects.map(p => p.hours);
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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


@endsection
