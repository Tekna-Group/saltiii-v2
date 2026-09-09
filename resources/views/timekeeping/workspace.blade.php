@extends('layouts.header')

@section('title', 'My Timesheet | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-operations.css') }}" rel="stylesheet" />
@endsection

@section('content')
@php
    $totalHours = $activities->sum('hours');
    $activeDays = $activities->pluck('timekeeping_date')->filter()->unique()->count();
    $hourlyRate = optional($users->first()->salary)->salary ?? 0;
    $estimatedPay = $totalHours * $hourlyRate;
    $today = date('Y-m-d');
@endphp

<div class="operations-page timesheet-page">
    <header class="operations-header">
        <div><span class="operations-eyebrow">Attendance and timekeeping</span><h1>My recorded work</h1><p>Review daily entries, weekly totals, and the work included in each period.</p></div>
        <button type="button" class="btn operations-primary" data-bs-toggle="modal" data-bs-target="#addActivity"><i class="ri-add-line" aria-hidden="true"></i> Log time</button>
    </header>

    <section class="operations-toolbar" aria-label="Timesheet period">
        <form method="GET" action="{{ url('/my-timekeeping') }}">
            <label for="timesheetFrom"><span>From</span><input id="timesheetFrom" type="date" name="date_from" value="{{ $date_from }}" required></label>
            <label for="timesheetTo"><span>To</span><input id="timesheetTo" type="date" name="date_to" value="{{ $date_to }}" required></label>
            <button type="submit" class="btn btn-primary"><i class="ri-filter-3-line" aria-hidden="true"></i> Apply period</button>
        </form>
        <button type="button" class="operations-print" onclick="window.print()"><i class="ri-printer-line" aria-hidden="true"></i> Print</button>
    </section>

    <section class="operations-signal-strip" aria-label="Timesheet summary">
        <div><small>Total hours</small><strong>{{ number_format($totalHours, 1) }}h</strong><span>{{ date('M j', strtotime($date_from)) }}–{{ date('M j, Y', strtotime($date_to)) }}</span></div>
        <div><small>Active days</small><strong>{{ $activeDays }}</strong><span>Days with recorded work</span></div>
        <div><small>Time entries</small><strong>{{ $activities->count() }}</strong><span>Across assigned work</span></div>
        <div><small>Estimated gross</small><strong>₱{{ number_format($estimatedPay, 2) }}</strong><span>Based on current hourly rate</span></div>
    </section>

    <section class="timesheet-week" aria-labelledby="timesheet-week-title">
        <header class="operations-section-head"><div><span class="operations-eyebrow">Period view</span><h2 id="timesheet-week-title">Hours by day</h2></div></header>
        <div class="timesheet-days">
            @foreach($date_ranges as $date)
                @php
                    $dayEntries = $activities->where('timekeeping_date', $date);
                    $dayHours = $dayEntries->sum('hours');
                @endphp
                <article class="timesheet-day {{ $date === $today ? 'is-today' : '' }} {{ $dayHours ? 'has-time' : '' }}"><header><span>{{ date('D', strtotime($date)) }}</span><time datetime="{{ $date }}">{{ date('M j', strtotime($date)) }}</time></header><strong>{{ $dayHours ? number_format($dayHours, 1).'h' : '—' }}</strong><div class="day-hours-bar"><i style="height:{{ min(100, max(5, ($dayHours / 8) * 100)) }}%"></i></div><small>{{ $dayEntries->count() }} {{ str_plural('entry', $dayEntries->count()) }}</small></article>
            @endforeach
        </div>
    </section>

    <section class="operations-register" aria-labelledby="time-entry-title">
        <header class="operations-section-head"><div><span class="operations-eyebrow">Work log</span><h2 id="time-entry-title">Time entries</h2></div><span>{{ $activities->count() }} records</span></header>
        <div class="operations-table-wrap"><table class="operations-table"><thead><tr><th>Date</th><th>Project and task</th><th>Work completed</th><th>Hours</th><th>Amount</th></tr></thead><tbody>
            @forelse($activities->sortByDesc('date') as $activity)
                <tr><td><time datetime="{{ $activity->date }}">{{ date('M j, Y', strtotime($activity->date)) }}</time><small>{{ date('l', strtotime($activity->date)) }}</small></td><td><strong>{{ optional($activity->project)->name ?: 'No project' }}</strong><small>{{ optional($activity->task)->title ?: 'General work' }}</small></td><td>{{ $activity->activity }}</td><td><strong>{{ number_format($activity->hours, 2) }}h</strong></td><td>₱{{ number_format($activity->hours * $hourlyRate, 2) }}</td></tr>
            @empty
                <tr><td colspan="5"><div class="operations-empty"><i class="ri-time-line" aria-hidden="true"></i><div><strong>No time recorded for this period</strong><p>Log completed work or choose another date range.</p></div><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActivity">Log time</button></div></td></tr>
            @endforelse
        </tbody><tfoot><tr><td colspan="3">Period total</td><td>{{ number_format($totalHours, 2) }}h</td><td>₱{{ number_format($estimatedPay, 2) }}</td></tr></tfoot></table></div>
    </section>
</div>

@include('home.add_activity')
@endsection

@section('js')
<script>document.querySelectorAll('.timesheet-page input[type="date"]').forEach(function(input){input.addEventListener('change',function(){var from=document.getElementById('timesheetFrom');var to=document.getElementById('timesheetTo');if(from.value&&to.value){to.min=from.value;from.max=to.value;}})});</script>
@endsection
