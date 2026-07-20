<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- @laravelPWA --}}
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{url('images/Favicon.png')}}">

    <!-- Layout config Js -->
    <script src="{{asset('inside_css/assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('inside_css/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('inside_css/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('inside_css/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('inside_css/assets/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
 
    {{-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css"> --}}
    @yield('css')
    <style>
        .helpdesk-link-wrapper {
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url("{{ asset('images/loader.gif') }}") 50% 50% no-repeat white;
            opacity: .8;
            background-size: 120px 120px;
        }   

        </style>
        <script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '4242328826038076');
fbq('track', 'PageView');
@if(session()->pull('fb_start_trial'))
fbq('track', 'StartTrial', {
    value: '0.00',
    currency: 'USD',
    predicted_ltv: '0.00'
});
@endif
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=4242328826038076&ev=PageView&noscript=1"
/></noscript>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-W84236P3');</script>
<!-- End Google Tag Manager -->
</head>
<body>
      <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W84236P3"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <div id = "loader" class="loader">
    </div>
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{url('/')}}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset('images/Favicon.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('images/Saltiii-Logo-White.svg')}}" alt="" height="45">
                                </span>
                            </a>

                            <a href="{{url('/')}}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{asset('images/Favicon.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('images/Saltiii-Logo-White.svg')}}" alt="" height="45">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                    
                    </div>

                    <div class="d-flex align-items-center">
                          <div class="ms-1 header-item d-none d-sm-flex">
                            <button id="startTourBtn" class="btn btn-soft-primary btn-sm d-flex align-items-center gap-1 m-2">
                                <i class="bx bx-help-circle fs-18"></i> Start Tour
                            </button>
                                 {{hours_today()}} hrs <i class=" bx bx-time-five fs-22"></i>
                                
                        </div>
                          <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                            <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                                <i class='bx bx-bell fs-22'></i>
                                <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">{{notifications()->count()}}<span class="visually-hidden">unread messages</span></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                                <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                    <div class="p-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                            </div>
                                            <div class="col-auto dropdown-tabs">
                                                <span class="badge bg-light text-body fs-13"> {{notifications()->count()}} New</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-2 pt-2">
                                        <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab" aria-selected="true">
                                                    All ({{notifications()->count()}})
                                                </a>
                                            </li>
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab" aria-selected="false">
                                                    Alerts
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>

                                <div class="tab-content position-relative" id="notificationItemsTabContent">
                                    <!-- All Notifications Tab -->
                                    <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2">
                                
                                            @forelse(notifications() as $notification)
                                                <div class="text-reset notification-item d-block dropdown-item position-relative">
                                                    <div class="d-flex">
                                                        <!-- Icon / Avatar -->
                                                        <div class="avatar-xs me-3 flex-shrink-0">
                                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                                <i class="bx bx-badge-check"></i>
                                                            </span>
                                                        </div>
                                
                                                        <!-- Notification Content -->
                                                        <div class="flex-grow-1">
                                                            <a href="{{ url('/view-project/view-task/' . $notification->data['task_id']) }}" class="stretched-link">
                                                                <h6 class="mt-0 mb-2 lh-base">
                                                                    <strong>{{ $notification->data['tagger_name'] }}</strong> 
                                                                    mentioned you in 
                                                                    <span class="text-secondary">{{ $notification->data['task_title'] }}</span>
                                                                </h6>
                                                            </a>
                                
                                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                                <span>
                                                                    <i class="mdi mdi-clock-outline"></i>
                                                                    {{ $notification->created_at->diffForHumans() }}
                                                                </span>
                                                            </p>
                                
                                                            <p class="mb-0 text-muted small">
                                                                Comment: "{!! $notification->data['comment_text'] !!}"
                                                            </p>
                                                        </div>
                                
                                                        <!-- Checkbox for bulk selection -->
                                                       
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center text-muted py-3">
                                                    No new notifications
                                                </div>
                                            @endforelse
                                
                                        </div>
                                    </div>
                                
                                    <!-- Alerts Tab (Optional Future Use) -->
                                    <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel" aria-labelledby="alerts-tab"></div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="{{asset(auth()->user()->avatar)}}" onerror="this.src='{{url('images/Favicon.png')}}';" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{current(explode(' ',auth()->user()->name))}}</span>
                                        {{-- <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Founder</span> --}}
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{current(explode(' ',auth()->user()->name))}}!</h6>
                                <a class="dropdown-item" href="{{url('/my-profile')}}" ><i class="mdi mdi-account-outline  text-muted fs-6 align-middle me-1"></i> <span class="align-middle">My Profile</span></a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editUserPassword"><i class="mdi mdi-key text-muted fs-6 align-middle me-1"></i> <span class="align-middle">Change Password</span></a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeAvatar"><i class="mdi mdi-file-image text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Change Avatar</span></a>
                              <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ url('invoices') }}">
                                    <i class="mdi mdi-receipt-text text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Invoices</span>
                                </a>
                              <a class="dropdown-item" href="{{ url('/subscription-plan') }}">
                                <i class="mdi mdi-crown-outline text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">Subscription Plan</span>
                            </a>
                              <div class="dropdown-divider"></div>
                               <a class="dropdown-item" href="{{ route('logout') }}" onclick="logout(); show();"> <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                               <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

<!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{url('/')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('images/Favicon.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('images/Saltiii-Logo-White.svg')}}" alt="" height="45">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{url('/')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{asset('images/Favicon.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('images/Saltiii-Logo-White.svg')}}" alt="" height="45">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>
    
        
            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav text-center" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu" style='font-size:20px;'>Menu</span></li>
                
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('/') || request()->is('dashboard') || request()->is('home') ? 'active' : '' }}" href="{{url('/')}}">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('projects') ? 'active' : '' }}" href="{{url('/projects')}}">
                                <i class="ri-list-check"></i> <span data-key="t-dashboards">Projects</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('ai-workflow-diagram') ? 'active' : '' }}" href="{{url('/ai-workflow-diagram')}}">
                                <i class="ri-flow-chart"></i> <span data-key="t-ai-diagram">AI Diagram Test</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('tasks') ? 'active' : '' }}" href="{{url('/tasks')}}">
                                <i class="ri-check-line"></i>
                                <span data-key="t-dashboards">Tasks</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('my-timekeeping') ? 'active' : '' }}" href="{{url('/my-timekeeping')}}">
                                <i class="ri-time-line"></i> <span data-key="t-dashboards">Timesheet</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('my-payslips') ? 'active' : '' }}" href="{{url('/my-payslips')}}">
                                <i class="ri-money-dollar-circle-line"></i> <span data-key="t-my-payslips">My Payslips</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('team-groups') ? 'active' : '' }}" href="{{url('/team-groups')}}">
                                <i class="ri-team-line"></i> <span data-key="t-team-groups">Team Group</span>
                            </a>
                        </li>
                
                        @if((auth()->user()->role == "Timekeeper") || (auth()->user()->role == "Admin"))
                        <li class="menu-title"><span data-key="t-menu" style='font-size:20px;'>Timekeeper</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('timekeeping') ? 'active' : '' }}" href="{{url('/timekeeping')}}">
                                <i class="ri-time-line"></i> <span data-key="t-dashboards">Timekeeping</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('timekeeping/posted') ? 'active' : '' }}" href="{{url('/timekeeping/posted')}}">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-dashboards">Posted Report</span>
                            </a>
                        </li>

                        <li class="menu-title"><span data-key="t-menu" style='font-size:20px;'>Payroll</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('payslips') || request()->is('payslips/*') ? 'active' : '' }}" href="{{url('/payslips')}}">
                                <i class="ri-money-dollar-circle-line"></i> <span data-key="t-payslips">Payslips</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('token-transfer') ? 'active' : '' }}" href="{{url('/token-transfer')}}">
                                <i class="ri-exchange-dollar-line"></i> <span data-key="t-token-transfer">Token Transfer</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->role == "Admin")
                        <li class="menu-title"><span data-key="t-menu" style='font-size:20px;'>Admin</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('users') ? 'active' : '' }}" href="{{url('/users')}}">
                                <i class="ri-team-fill"></i> <span data-key="t-dashboards">Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('reports') ? 'active' : '' }}" href="{{url('/reports')}}">
                                <i class="ri-file-list-3-fill"></i> <span data-key="t-dashboards">Reports</span>
                            </a>
                        </li>
                        @endif
                
                    </ul>
                    {{-- <div class="helpdesk-link-wrapper mt-auto">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link menu-link bg-white" href="https://saltiii.freshdesk.com/support/home" target="_blank">
                                    <i class="ri-customer-service-2-line"></i> 
                                    <span data-key="t-submit-ticket" class="text-warning">Need Support?</span>
                                </a>
                            </li>
                        </ul>
                    </div> --}}
                </div>
                
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                    <h4 class="mb-sm-0">{{Route::current()->getName()}}</h4>
        
                                    
                                </div>
                            </div>
                        </div>
                        @yield('content')
                    </div>
                </div>
            
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                {{date('Y')}} © SALTiii
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    Design & Develop by 
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
    </div>


    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

  

    <!-- Theme Settings -->
 @include('change_password')
 @include('change_avatar')
        @include('sweetalert::alert')
    <!-- JAVASCRIPT -->
    <script src="{{asset('inside_css/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('inside_css/assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('inside_css/assets/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('inside_css/assets/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('inside_css/assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    <script src="{{asset('inside_css/assets/js/plugins.js')}}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
     
        
            startTourBtn.addEventListener('click', () => {
                introJs().setOptions({
                    nextLabel: 'Next →',
                    prevLabel: '← Back',
                    skipLabel: 'Skip',
                    doneLabel: 'Finish',
                    showProgress: true,
                    showBullets: false,
                    overlayOpacity: 0.6,
                    disableInteraction: false,
                }).start();
            });
        });
        </script>
    <!-- apexcharts -->

   @yield('js')
    <!-- App js -->
    <script src="{{asset('inside_css/assets/js/app.js')}}"></script>
 
    <script>
         function show() {
            document.getElementById("loader").style.display = "block";
        }
        function logout() {
        event.preventDefault();
        document.getElementById('logout-form').submit();
    }

</script>
<script>
    window.addEventListener('load', function() {
        document.getElementById('loader').style.display = 'none';
    });
</script>
</body>
</html>
