<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0c3442">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    {{-- @laravelPWA --}}
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'SALTiii'))</title>
    <link rel="shortcut icon" href="{{url('images/Favicon.png')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

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
    <link href="{{asset('inside_css/assets/css/saltiii-refresh.css')}}" rel="stylesheet" type="text/css" />
 
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
      <a class="salt-skip-link" href="#workspace-content">Skip to workspace content</a>
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
                            <a href="{{url('/dashboard')}}" class="logo logo-dark" aria-label="SALTiii dashboard">
                                <span class="logo-sm">
                                    <img src="{{asset('images/Favicon.png')}}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset('images/Saltiii-Logo-White.svg')}}" alt="" height="45">
                                </span>
                            </a>

                            <a href="{{url('/dashboard')}}" class="logo logo-light" aria-label="SALTiii dashboard">
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

                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="global-nav-trigger" id="global-nav-trigger" aria-haspopup="dialog" aria-controls="salt-command" title="Jump to a page">
                            <i class="ri-search-line" aria-hidden="true"></i><span>Jump to</span><kbd>Ctrl K</kbd>
                        </button>
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
                               <a class="dropdown-item" href="{{ route('logout') }}" onclick="logout(event); show();"> <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
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
                <a href="{{url('/dashboard')}}" class="logo logo-dark" aria-label="SALTiii dashboard">
                    <span class="logo-sm">
                        <img src="{{asset('images/Favicon.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('images/Saltiii-Logo-White.svg')}}" alt="" height="45">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{url('/dashboard')}}" class="logo logo-light" aria-label="SALTiii dashboard">
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
                        <li class="menu-title"><span data-key="t-menu">Workspace</span></li>
                
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('dashboard') || request()->is('home') ? 'active' : '' }}" href="{{url('/dashboard')}}">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('projects') || request()->is('view-project*') ? 'active' : '' }}" href="{{url('/projects')}}">
                                <i class="ri-list-check"></i> <span data-key="t-dashboards">Projects</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('ai-workflow-diagram') || request()->is('diagram/*') ? 'active' : '' }}" href="{{url('/ai-workflow-diagram')}}">
                                <i class="ri-flow-chart"></i> <span data-key="t-ai-diagram">Process Designer</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('tasks') || request()->is('view-task*') ? 'active' : '' }}" href="{{url('/tasks')}}">
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
                            <a class="nav-link menu-link {{ request()->is('leave*') ? 'active' : '' }}" href="{{url('/leave')}}">
                                <i class="ri-calendar-event-line"></i> <span>Leave</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('my-payslips') ? 'active' : '' }}" href="{{url('/my-payslips')}}">
                                <i class="ri-money-dollar-circle-line"></i> <span data-key="t-my-payslips">My Pay</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('team-groups*') ? 'active' : '' }}" href="{{url('/team-groups')}}">
                                <i class="ri-team-line"></i> <span data-key="t-team-groups">Teams</span>
                            </a>
                        </li>
                
                        @if((auth()->user()->role == "Timekeeper") || (auth()->user()->role == "Admin"))
                        <li class="menu-title"><span data-key="t-menu">Operations</span></li>
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

                        <li class="menu-title"><span data-key="t-menu">Payments</span></li>
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

                        @if(in_array(auth()->user()->role, ['Admin', 'Project Lead'], true))
                        <li class="menu-title"><span data-key="t-menu">Administration</span></li>
                        @endif
                        @if(auth()->user()->role == "Admin")
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('users') ? 'active' : '' }}" href="{{url('/users')}}">
                                <i class="ri-team-fill"></i> <span data-key="t-dashboards">People</span>
                            </a>
                        </li>
                        @endif
                        @if(in_array(auth()->user()->role, ['Admin', 'Project Lead'], true))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('reports') ? 'active' : '' }}" href="{{url('/reports')}}">
                                <i class="ri-file-list-3-fill"></i> <span data-key="t-dashboards">Reports</span>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->role == "Admin")
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->is('settings') ? 'active' : '' }}" href="{{url('/settings')}}">
                                <i class="ri-settings-3-line"></i> <span>Settings</span>
                            </a>
                        </li>
                        @endif
                
                    </ul>
                    <div class="sidebar-support"><strong>Need a hand?</strong>Visit the help center or contact the SALTiii team.<br><a href="https://saltiii.freshdesk.com/support/home" target="_blank" rel="noopener"><i class="ri-customer-service-2-line"></i> Open help center</a></div>
                </div>
                
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>
        <div class="main-content" id="workspace-content" tabindex="-1">

            <div class="page-content">
                <div class="container-fluid">
                        @php
                            $pageTitle = Route::currentRouteName() ?: 'Workspace';
                            $pageDescription = 'Manage your work in one connected workspace.';
                            if (request()->is('dashboard') || request()->is('home')) { $pageTitle = 'Dashboard'; $pageDescription = 'Your priorities, progress, and recent work at a glance.'; }
                            elseif (request()->is('projects')) { $pageTitle = 'Projects'; $pageDescription = 'Plan, organize, and keep every initiative moving.'; }
                            elseif (request()->is('view-project*')) { $pageTitle = 'Project workspace'; $pageDescription = 'Coordinate tasks, people, updates, and delivery.'; }
                            elseif (request()->is('tasks') || request()->is('view-task*')) { $pageTitle = 'Tasks'; $pageDescription = 'See what needs attention and move work forward.'; }
                            elseif (request()->is('ai-workflow-diagram') || request()->is('diagram/*')) { $pageTitle = 'AI Workflow Builder'; $pageDescription = 'Generate, refine, and export editable process diagrams.'; }
                            elseif (request()->is('my-timekeeping')) { $pageTitle = 'My Timesheet'; $pageDescription = 'Review and manage the time behind your work.'; }
                            elseif (request()->is('leave*')) { $pageTitle = 'Leave'; $pageDescription = 'Coordinate time away and keep team availability clear.'; }
                            elseif (request()->is('timekeeping/posted')) { $pageTitle = 'Posted Time'; $pageDescription = 'Review submitted and approved time records.'; }
                            elseif (request()->is('timekeeping')) { $pageTitle = 'Timekeeping'; $pageDescription = 'Review team hours and prepare approved work for payment.'; }
                            elseif (request()->is('my-payslips')) { $pageTitle = 'My Pay'; $pageDescription = 'Access your pay history and detailed payslips.'; }
                            elseif (request()->is('payslips*')) { $pageTitle = 'Payroll'; $pageDescription = 'Manage pay runs, adjustments, and employee records.'; }
                            elseif (request()->is('team-groups*')) { $pageTitle = 'Teams'; $pageDescription = 'Organize people, invitations, and shared billing groups.'; }
                            elseif (request()->is('token-transfer')) { $pageTitle = 'Payments'; $pageDescription = 'Prepare and review secure team transfers.'; }
                            elseif (request()->is('users')) { $pageTitle = 'People'; $pageDescription = 'Manage team access, roles, and account details.'; }
                            elseif (request()->is('reports')) { $pageTitle = 'Reports'; $pageDescription = 'Understand workload, completion, and team performance.'; }
                            elseif (request()->is('settings')) { $pageTitle = 'System Settings'; $pageDescription = 'Manage workspace-wide defaults and operational rules.'; }
                            elseif (request()->is('my-profile') || request()->is('view-profile*')) { $pageTitle = 'Profile'; $pageDescription = 'Review personal details, activity, and account settings.'; }
                            elseif (request()->is('invoices*')) { $pageTitle = 'Invoices'; $pageDescription = 'View billing history and payment records.'; }
                            elseif (request()->is('subscription-plan')) { $pageTitle = 'Plan & Billing'; $pageDescription = 'Manage your SALTiii subscription and plan details.'; }
                        @endphp
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                    <div><div class="salt-breadcrumb"><a href="{{ url('/dashboard') }}">Workspace</a><span aria-hidden="true">/</span><span>{{ $pageTitle }}</span></div><h4 class="mb-sm-0">{{ $pageTitle }}</h4><p class="page-heading-copy">{{ $pageDescription }}</p></div>
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
                                {{date('Y')}} &copy; SALTiii
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    Work flows better in one place.
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
    </div>


    <nav class="salt-mobile-dock" aria-label="Mobile workspace navigation">
        <a class="{{ request()->is('dashboard') || request()->is('home') ? 'active' : '' }}" href="{{ url('/dashboard') }}"><i class="ri-dashboard-2-line" aria-hidden="true"></i><span>Today</span></a>
        <a class="{{ request()->is('tasks') || request()->is('view-task*') ? 'active' : '' }}" href="{{ url('/tasks') }}"><i class="ri-list-check-2" aria-hidden="true"></i><span>Tasks</span></a>
        <a class="{{ request()->is('my-timekeeping') ? 'active' : '' }}" href="{{ url('/my-timekeeping') }}"><i class="ri-time-line" aria-hidden="true"></i><span>Time</span></a>
        <a class="{{ request()->is('leave*') ? 'active' : '' }}" href="{{ url('/leave') }}"><i class="ri-calendar-event-line" aria-hidden="true"></i><span>Leave</span></a>
        <button type="button" id="mobile-more-menu"><i class="ri-menu-4-line" aria-hidden="true"></i><span>More</span></button>
    </nav>


    <div class="salt-command" id="salt-command" role="dialog" aria-modal="true" aria-labelledby="salt-command-title" hidden>
        <div class="salt-command-dialog">
            <h2 id="salt-command-title" class="visually-hidden">Jump to a page</h2>
            <div class="salt-command-search"><i class="ri-search-line" aria-hidden="true"></i><input id="salt-command-input" type="search" placeholder="Search pages…" autocomplete="off" aria-label="Search available pages"><button type="button" id="salt-command-close" aria-label="Close page search">&times;</button></div>
            <div class="salt-command-results" id="salt-command-results"></div>
            <div class="salt-command-hint"><span>Type to filter pages</span><span>Enter to open · Esc to close</span></div>
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
            const startTourBtn = document.getElementById('startTourBtn');
            if (startTourBtn && typeof introJs === 'function') startTourBtn.addEventListener('click', () => {
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
        function logout(event) {
        if (event) event.preventDefault();
        document.getElementById('logout-form').submit();
    }

</script>
<script>
    window.addEventListener('load', function() {
        document.getElementById('loader').style.display = 'none';
    });
</script>
<script>
(function(){
    var trigger=document.getElementById('global-nav-trigger');
    var palette=document.getElementById('salt-command');
    var input=document.getElementById('salt-command-input');
    var results=document.getElementById('salt-command-results');
    var closeButton=document.getElementById('salt-command-close');
    var links=[];
    var lastFocus=null;
    if(!trigger||!palette||!input||!results)return;
    document.querySelectorAll('#navbar-nav .menu-link').forEach(function(link){
        var label=(link.textContent||'').trim().replace(/\s+/g,' ');
        if(!label||!link.href)return;
        var icon=link.querySelector('i');
        links.push({label:label,href:link.href,icon:icon?icon.className:'ri-arrow-right-line'});
    });
    function render(query){
        var term=(query||'').trim().toLowerCase();
        var matches=links.filter(function(item){return item.label.toLowerCase().indexOf(term)!==-1});
        results.innerHTML='';
        if(!matches.length){results.innerHTML='<div class="salt-command-empty">No matching page found.</div>';return}
        matches.forEach(function(item,index){
            var button=document.createElement('button');
            var icon=document.createElement('i');
            var label=document.createElement('span');
            var hint=document.createElement('small');
            button.type='button';button.className='salt-command-item'+(index===0?' active':'');
            icon.className=item.icon;icon.setAttribute('aria-hidden','true');label.textContent=item.label;hint.textContent='Open';
            button.appendChild(icon);button.appendChild(label);button.appendChild(hint);
            button.addEventListener('click',function(){window.location.href=item.href});results.appendChild(button);
        });
    }
    function openPalette(){lastFocus=document.activeElement;palette.hidden=false;document.body.style.overflow='hidden';input.value='';render('');window.setTimeout(function(){input.focus()},0)}
    function closePalette(){palette.hidden=true;document.body.style.overflow='';if(lastFocus)lastFocus.focus()}
    trigger.addEventListener('click',openPalette);closeButton.addEventListener('click',closePalette);input.addEventListener('input',function(){render(input.value)});
    input.addEventListener('keydown',function(event){if(event.key==='Enter'){var first=results.querySelector('.salt-command-item');if(first){event.preventDefault();first.click()}}});
    palette.addEventListener('click',function(event){if(event.target===palette)closePalette()});
    document.addEventListener('keydown',function(event){if((event.ctrlKey||event.metaKey)&&event.key.toLowerCase()==='k'){event.preventDefault();palette.hidden?openPalette():closePalette()}else if(event.key==='Escape'&&!palette.hidden){closePalette()}});
}());
</script>
<script>
(function(){var more=document.getElementById('mobile-more-menu');var menu=document.getElementById('topnav-hamburger-icon');if(more&&menu){more.addEventListener('click',function(){menu.click()})}}());
</script>
</body>
</html>
