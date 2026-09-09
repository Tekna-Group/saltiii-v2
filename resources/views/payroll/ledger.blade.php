@extends('layouts.header')

@section('title', 'Payroll | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-operations.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="operations-page payroll-page">
    <header class="operations-header"><div><span class="operations-eyebrow">Payroll operations</span><h1>Pay runs and employee records</h1><p>Review approved time, adjustments, payment status, and detailed payslips.</p></div><a class="btn operations-primary" href="{{ url('/timekeeping') }}"><i class="ri-time-line" aria-hidden="true"></i> Prepare time</a></header>

    <section class="operations-toolbar" aria-label="Payroll filters"><form method="GET" action="{{ route('Payslip') }}"><label><span>Employee</span><select name="user_id"><option value="ALL">All employees</option>@foreach($users as $employee)<option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>@endforeach</select></label><label><span>Status</span><select name="status"><option value="ALL">All statuses</option><option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option><option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option></select></label><label><span>From</span><input type="date" name="date_from" value="{{ request('date_from') }}"></label><label><span>To</span><input type="date" name="date_to" value="{{ request('date_to') }}"></label><button type="submit" class="btn btn-primary"><i class="ri-filter-3-line" aria-hidden="true"></i> Apply</button><a class="btn btn-light" href="{{ route('Payslip') }}">Clear</a></form><button type="button" class="operations-print" onclick="window.print()"><i class="ri-printer-line" aria-hidden="true"></i> Print ledger</button></section>

    <section class="operations-signal-strip" aria-label="Payroll summary"><div><small>Approved hours</small><strong>{{ number_format($totalHours, 1) }}h</strong><span>In this view</span></div><div><small>Gross payroll</small><strong>₱{{ number_format($totalAmount, 2) }}</strong><span>Before adjustments</span></div><div><small>Paid</small><strong>₱{{ number_format($totalPaid, 2) }}</strong><span>Completed transfers</span></div><div><small>Ready to pay</small><strong>₱{{ number_format($totalPending, 2) }}</strong><span>Approved postings</span></div></section>

    <section class="operations-register" aria-labelledby="payroll-ledger-title"><header class="operations-section-head"><div><span class="operations-eyebrow">Payment register</span><h2 id="payroll-ledger-title">Payroll ledger</h2></div><span>{{ $paymentPostings->count() }} records</span></header><div class="operations-table-wrap"><table class="operations-table payroll-ledger"><thead><tr><th>Employee</th><th>Reference</th><th>Pay period</th><th>Hours</th><th>Gross</th><th>Adjustments</th><th>Net pay</th><th>Status</th><th>Action</th></tr></thead><tbody>
        @forelse($paymentPostings as $posting)
            @php($adds = $posting->adjustments->where('type', 'add')->sum('amount'))
            @php($deducts = $posting->adjustments->where('type', 'deduct')->sum('amount'))
            <tr><td><strong>{{ optional($posting->user)->name ?: 'Unknown employee' }}</strong><small>{{ optional($posting->user)->email ?: 'No email' }}</small></td><td><span class="ledger-reference">{{ $posting->reference_number }}</span></td><td>{{ $posting->date_from ? $posting->date_from->format('M j') : '—' }}–{{ $posting->date_to ? $posting->date_to->format('M j, Y') : '—' }}</td><td>{{ number_format($posting->total_hours, 2) }}h</td><td>₱{{ number_format($posting->total_amount, 2) }}</td><td>@if($adds)<span class="ledger-add">+₱{{ number_format($adds, 2) }}</span>@endif @if($deducts)<span class="ledger-deduct">−₱{{ number_format($deducts, 2) }}</span>@endif @if(!$adds && !$deducts)<span class="text-muted">—</span>@endif</td><td><strong>₱{{ number_format($posting->net_amount, 2) }}</strong></td><td><span class="pay-status status-{{ strtolower($posting->status) }}">{{ $posting->status }}</span></td><td><a class="task-open" href="{{ route('Payslip.view', $posting->id) }}" aria-label="Open payslip {{ $posting->reference_number }}"><i class="ri-arrow-right-line" aria-hidden="true"></i></a></td></tr>
        @empty<tr><td colspan="9"><div class="operations-empty"><i class="ri-bank-card-line" aria-hidden="true"></i><div><strong>No payroll records in this view</strong><p>Adjust the filters or prepare approved timekeeping records.</p></div></div></td></tr>@endforelse
    </tbody><tfoot><tr><td colspan="3">Totals</td><td>{{ number_format($totalHours, 2) }}h</td><td>₱{{ number_format($totalAmount, 2) }}</td><td></td><td>₱{{ number_format($totalNet, 2) }}</td><td colspan="2"></td></tr></tfoot></table></div></section>
</div>
@endsection
