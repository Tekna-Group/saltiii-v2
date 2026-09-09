@extends('layouts.header')

@section('title', 'System Settings | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-operations.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="operations-page settings-page">
    <header class="operations-header"><div><span class="operations-eyebrow">System administration</span><h1>Workspace settings</h1><p>Set the defaults used across employee, timekeeping, leave, and payroll workflows.</p></div><button class="btn operations-primary" type="submit" form="workspaceSettingsForm"><i class="ri-save-line" aria-hidden="true"></i> Save settings</button></header>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Settings were not saved.</strong> {{ $errors->first() }}</div>@endif

    <form id="workspaceSettingsForm" method="POST" action="{{ route('settings.update') }}" class="settings-layout">@csrf
        <nav class="settings-nav" aria-label="Settings sections"><a href="#workspaceIdentity" class="is-active"><i class="ri-building-4-line" aria-hidden="true"></i><span><strong>Workspace</strong><small>Name, timezone, and display</small></span></a><a href="#timeRules"><i class="ri-time-line" aria-hidden="true"></i><span><strong>Time and attendance</strong><small>Workweek and entry defaults</small></span></a><a href="#leaveRules"><i class="ri-calendar-event-line" aria-hidden="true"></i><span><strong>Leave workflow</strong><small>Review responsibility</small></span></a><a href="#notifications"><i class="ri-notification-3-line" aria-hidden="true"></i><span><strong>Notifications</strong><small>Operational summaries</small></span></a></nav>

        <main class="settings-sections">
            <section id="workspaceIdentity" class="settings-section"><header><span class="settings-section-icon"><i class="ri-building-4-line" aria-hidden="true"></i></span><div><h2>Workspace identity</h2><p>Used in system headings, reports, and exported records.</p></div></header><div class="settings-fields"><label class="settings-field is-wide" for="companyName"><span>Company or workspace name</span><input class="form-control" id="companyName" name="company_name" value="{{ old('company_name', $settings->company_name) }}" maxlength="120" required></label><label class="settings-field" for="workspaceTimezone"><span>Timezone</span><select class="form-select" id="workspaceTimezone" name="timezone" required>@foreach(['Asia/Manila'=>'Asia/Manila (Philippines)','Asia/Taipei'=>'Asia/Taipei','Asia/Singapore'=>'Asia/Singapore','UTC'=>'UTC'] as $value=>$label)<option value="{{ $value }}" {{ old('timezone', $settings->timezone) === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></label><label class="settings-field" for="dateFormat"><span>Date format</span><select class="form-select" id="dateFormat" name="date_format" required>@foreach(['M j, Y'=>'Sep 9, 2026','d M Y'=>'09 Sep 2026','Y-m-d'=>'2026-09-09'] as $value=>$label)<option value="{{ $value }}" {{ old('date_format', $settings->date_format) === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></label></div></section>

            <section id="timeRules" class="settings-section"><header><span class="settings-section-icon"><i class="ri-time-line" aria-hidden="true"></i></span><div><h2>Time and attendance</h2><p>Defaults for weekly timesheets and payroll calculations.</p></div></header><div class="settings-fields"><label class="settings-field" for="workweekStart"><span>Workweek starts</span><select class="form-select" id="workweekStart" name="workweek_start" required>@foreach(['Monday','Sunday','Saturday'] as $day)<option value="{{ $day }}" {{ old('workweek_start', $settings->workweek_start) === $day ? 'selected' : '' }}>{{ $day }}</option>@endforeach</select></label><label class="settings-field" for="standardHours"><span>Standard hours per day</span><input class="form-control" id="standardHours" type="number" name="standard_hours" min="1" max="24" step="0.25" value="{{ old('standard_hours', $settings->standard_hours) }}" required></label><label class="settings-field" for="workspaceCurrency"><span>Payroll currency</span><select class="form-select" id="workspaceCurrency" name="currency" required>@foreach(['PHP'=>'PHP — Philippine peso','USD'=>'USD — US dollar','USDC'=>'USDC'] as $value=>$label)<option value="{{ $value }}" {{ old('currency', $settings->currency) === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></label><label class="settings-switch"><input type="checkbox" name="require_time_notes" value="1" {{ old('require_time_notes', $settings->require_time_notes) ? 'checked' : '' }}><span aria-hidden="true"></span><div><strong>Require a note for time entries</strong><small>Employees must describe the work completed.</small></div></label></div></section>

            <section id="leaveRules" class="settings-section"><header><span class="settings-section-icon"><i class="ri-calendar-event-line" aria-hidden="true"></i></span><div><h2>Leave workflow</h2><p>Choose who makes the first decision on employee requests.</p></div></header><div class="settings-fields"><label class="settings-field is-wide" for="leaveApprovalRole"><span>Default leave reviewer</span><select class="form-select" id="leaveApprovalRole" name="leave_approval_role" required><option value="Project Lead" {{ old('leave_approval_role', $settings->leave_approval_role) === 'Project Lead' ? 'selected' : '' }}>Project Lead</option><option value="Admin" {{ old('leave_approval_role', $settings->leave_approval_role) === 'Admin' ? 'selected' : '' }}>Administrator</option></select><small>Administrators can always review requests. Project Leads only see employees who share their projects.</small></label></div></section>

            <section id="notifications" class="settings-section"><header><span class="settings-section-icon"><i class="ri-notification-3-line" aria-hidden="true"></i></span><div><h2>Operational notifications</h2><p>Control recurring workspace summaries.</p></div></header><div class="settings-fields"><label class="settings-switch"><input type="checkbox" name="weekly_summary" value="1" {{ old('weekly_summary', $settings->weekly_summary) ? 'checked' : '' }}><span aria-hidden="true"></span><div><strong>Weekly work summary</strong><small>Send employees their task and time summary.</small></div></label></div></section>

            <footer class="settings-save-bar"><div><strong>Workspace-wide change</strong><span>These defaults affect future records and displays.</span></div><button type="submit" class="btn btn-primary"><i class="ri-save-line" aria-hidden="true"></i> Save settings</button></footer>
        </main>
    </form>
</div>
@endsection

@section('js')
<script>document.querySelectorAll('.settings-nav a').forEach(function(link){link.addEventListener('click',function(){document.querySelectorAll('.settings-nav a').forEach(function(item){item.classList.remove('is-active')});link.classList.add('is-active')})});</script>
@endsection
