@extends('layouts.header')

@section('title', 'My Pay | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-operations.css') }}" rel="stylesheet" />
@endsection

@section('content')
@php($lastPaid = $paymentPostings->where('status', 'Completed')->first())
<div class="operations-page pay-history-page">
    <header class="operations-header"><div><span class="operations-eyebrow">Personal payroll</span><h1>My pay history</h1><p>See pay periods, hours, adjustments, net amounts, and payment status.</p></div>@if($lastPaid)<a class="btn operations-primary" href="{{ route('Payslip.view', $lastPaid->id) }}"><i class="ri-file-download-line" aria-hidden="true"></i> Latest payslip</a>@endif</header>
    <section class="operations-signal-strip" aria-label="Pay summary"><div><small>Payslips</small><strong>{{ $paymentPostings->count() }}</strong><span>Recorded pay periods</span></div><div><small>Total hours</small><strong>{{ number_format($totalHours, 1) }}h</strong><span>Across all payslips</span></div><div><small>Total net pay</small><strong>₱{{ number_format($totalNet, 2) }}</strong><span>After adjustments</span></div><div><small>Last paid</small><strong>{{ $lastPaid && $lastPaid->approved_at ? $lastPaid->approved_at->format('M j') : '—' }}</strong><span>{{ $lastPaid ? $lastPaid->status : 'No completed payment' }}</span></div></section>
    <section class="pay-history" aria-labelledby="pay-history-title"><header class="operations-section-head"><div><span class="operations-eyebrow">Payslip register</span><h2 id="pay-history-title">Pay periods</h2></div><span>{{ $paymentPostings->count() }} records</span></header><div class="pay-history-list">
        @forelse($paymentPostings as $posting)
            @php($adds = $posting->adjustments->where('type', 'add')->sum('amount'))
            @php($deducts = $posting->adjustments->where('type', 'deduct')->sum('amount'))
            <a href="{{ route('Payslip.view', $posting->id) }}" class="pay-history-row"><span class="pay-period"><small>Pay period</small><strong>{{ $posting->date_from ? $posting->date_from->format('M j') : '—' }}–{{ $posting->date_to ? $posting->date_to->format('M j, Y') : '—' }}</strong><em>{{ $posting->reference_number }}</em></span><span><small>Hours</small><strong>{{ number_format($posting->total_hours, 2) }}h</strong></span><span><small>Gross</small><strong>₱{{ number_format($posting->total_amount, 2) }}</strong></span><span><small>Adjustments</small><strong class="{{ $adds - $deducts < 0 ? 'text-danger' : '' }}">{{ $adds - $deducts >= 0 ? '+' : '−' }}₱{{ number_format(abs($adds - $deducts), 2) }}</strong></span><span class="pay-net"><small>Net pay</small><strong>₱{{ number_format($posting->net_amount, 2) }}</strong></span><span class="pay-status status-{{ strtolower($posting->status) }}">{{ $posting->status }}</span><i class="ri-arrow-right-line" aria-hidden="true"></i></a>
        @empty<div class="operations-empty"><i class="ri-file-list-3-line" aria-hidden="true"></i><div><strong>No payslips yet</strong><p>Completed payroll periods will appear here.</p></div></div>@endforelse
    </div></section>
</div>
@endsection
