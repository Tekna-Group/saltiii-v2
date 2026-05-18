@extends('layouts.header')
@section('css')
<style>
    .payslip-card { transition: box-shadow .15s; }
    .payslip-card:hover { box-shadow: 0 4px 18px rgba(64,81,137,.13); }
    .badge-Completed { background:#1a9b5f; color:#fff; }
    .badge-Approved  { background:#f7b84b; color:#fff; }
</style>
@endsection
@section('content')

{{-- Summary strip --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-start border-info border-3 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium small">Total Payslips</p>
                    <h3 class="mb-0 fw-semibold">{{ $paymentPostings->count() }}</h3>
                </div>
                <div class="avatar-sm"><span class="avatar-title bg-info-subtle rounded-circle fs-2"><i data-feather="file-text" class="text-info"></i></span></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-start border-success border-3 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium small">Total Hours</p>
                    <h3 class="mb-0 fw-semibold">{{ number_format($totalHours, 2) }}</h3>
                </div>
                <div class="avatar-sm"><span class="avatar-title bg-success-subtle rounded-circle fs-2"><i data-feather="clock" class="text-success"></i></span></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-start border-primary border-3 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium small">Total Net Pay</p>
                    <h3 class="mb-0 fw-semibold">₱ {{ number_format($totalNet, 2) }}</h3>
                </div>
                <div class="avatar-sm"><span class="avatar-title bg-primary-subtle rounded-circle fs-2"><i data-feather="dollar-sign" class="text-primary"></i></span></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-start border-warning border-3 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium small">Last Paid</p>
                    <h4 class="mb-0 fw-semibold">
                        @php $lastPaid = $paymentPostings->where('status','Completed')->first(); @endphp
                        {{ $lastPaid ? \Carbon\Carbon::parse($lastPaid->approved_at)->format('M d, Y') : '—' }}
                    </h4>
                </div>
                <div class="avatar-sm"><span class="avatar-title bg-warning-subtle rounded-circle fs-2"><i data-feather="check-circle" class="text-warning"></i></span></div>
            </div>
        </div>
    </div>
</div>

{{-- Payslip cards --}}
@if($paymentPostings->isEmpty())
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="ri-inbox-line fs-1 d-block mb-2"></i>
        No payslips found. Your payslips will appear here once payroll is processed.
    </div>
</div>
@else
<div class="row g-3">
    @foreach($paymentPostings as $posting)
    @php
        $netAmount = $posting->net_amount;
        $adds    = $posting->adjustments->where('type','add')->sum('amount');
        $deducts = $posting->adjustments->where('type','deduct')->sum('amount');
    @endphp
    <div class="col-xl-4 col-md-6">
        <div class="card payslip-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="font-monospace small text-muted">{{ $posting->reference_number }}</span>
                <span class="badge badge-{{ $posting->status }} px-2">{{ $posting->status }}</span>
            </div>
            <div class="card-body pb-2">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <p class="text-muted mb-0 small">Pay Period</p>
                        <div class="fw-medium">
                            @if($posting->date_from && $posting->date_to)
                                {{ \Carbon\Carbon::parse($posting->date_from)->format('M d') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($posting->date_to)->format('M d, Y') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="text-muted mb-0 small">Hours</p>
                        <div class="fw-medium">{{ number_format($posting->total_hours, 2) }} hrs</div>
                    </div>
                </div>

                <hr class="my-2">

                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <p class="text-muted mb-0 small">Gross Pay</p>
                        <div class="text-muted">₱ {{ number_format($posting->total_amount, 2) }}</div>
                        @if($adds > 0)
                        <div class="text-success small">+ ₱ {{ number_format($adds, 2) }} adjustments</div>
                        @endif
                        @if($deducts > 0)
                        <div class="text-danger small">- ₱ {{ number_format($deducts, 2) }} deductions</div>
                        @endif
                    </div>
                    <div class="text-end">
                        <p class="text-muted mb-0 small">Net Pay</p>
                        <div class="fs-5 fw-bold text-primary">₱ {{ number_format($netAmount, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    Approved: {{ $posting->approved_at ? \Carbon\Carbon::parse($posting->approved_at)->format('M d, Y') : '—' }}
                </small>
                <a href="{{ route('Payslip.view', $posting->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="ri-file-text-line me-1"></i>View Payslip
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
@section('js')
@endsection
