@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .stat-card { border-left: 4px solid; }
    .stat-card.total { border-color: #0ab39c; }
    .stat-card.paid  { border-color: #405189; }
    .stat-card.pending { border-color: #f7b84b; }
    .stat-card.hours  { border-color: #299cdb; }
    .badge-Completed { background-color: #1a9b5f; color:#fff; }
    .badge-Approved  { background-color: #f7b84b; color:#fff; }
    .badge-Pending   { background-color: #6c757d; color:#fff; }
</style>
@endsection
@section('content')

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card hours h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium">Total Hours</p>
                    <h3 class="mb-0 ff-secondary fw-semibold">{{ number_format($totalHours, 2) }}</h3>
                </div>
                <div class="avatar-sm">
                    <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                        <i data-feather="clock" class="text-info"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card total h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium">Total Amount</p>
                    <h3 class="mb-0 ff-secondary fw-semibold">₱ {{ number_format($totalAmount, 2) }}</h3>
                </div>
                <div class="avatar-sm">
                    <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                        <i data-feather="dollar-sign" class="text-success"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card paid h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium">Total Paid</p>
                    <h3 class="mb-0 ff-secondary fw-semibold">₱ {{ number_format($totalPaid, 2) }}</h3>
                </div>
                <div class="avatar-sm">
                    <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                        <i data-feather="check-circle" class="text-primary"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card pending h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-medium">Pending Amount</p>
                    <h3 class="mb-0 ff-secondary fw-semibold">₱ {{ number_format($totalPending, 2) }}</h3>
                </div>
                <div class="avatar-sm">
                    <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                        <i data-feather="alert-circle" class="text-warning"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Payment Report</h5>
            </div>
            <form method="GET" action="{{ route('Payslip') }}">
                <div class="card-body">
                    <div class="row gy-2 gx-3 align-items-end">
                        <div class="col-xl-3 col-md-4">
                            <label class="form-label mb-1">Employee</label>
                            <select class="form-control select2" name="user_id">
                                <option value="ALL">All Employees</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @if(request('user_id') == $u->id) selected @endif>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label mb-1">Status</label>
                            <select class="form-control" name="status">
                                <option value="ALL">All Status</option>
                                <option value="Completed" @if(request('status') == 'Completed') selected @endif>Completed</option>
                                <option value="Approved" @if(request('status') == 'Approved') selected @endif>Approved</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label mb-1">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-xl-2 col-md-4">
                            <label class="form-label mb-1">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="ri-search-line me-1"></i>Filter
                            </button>
                            <a href="{{ route('Payslip') }}" class="btn btn-secondary btn-sm ms-1">
                                <i class="ri-refresh-line me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Payment Records Table --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    Payment Records
                    <span class="badge bg-secondary ms-1">{{ $paymentPostings->count() }}</span>
                </h5>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="ri-printer-line me-1"></i>Print Report
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Reference</th>
                                <th>Employee</th>
                                <th>Pay Period</th>
                                <th class="text-end">Hours</th>
                                <th class="text-end">Gross (PHP)</th>
                                <th class="text-end">Adjustments</th>
                                <th class="text-end">Net Pay</th>
                                <th>Status</th>
                                <th>Approved At</th>
                                <th class="text-center">Payslip</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentPostings as $i => $posting)
                            @php
                                $adjAdds    = $posting->adjustments->where('type','add')->sum('amount');
                                $adjDeducts = $posting->adjustments->where('type','deduct')->sum('amount');
                                $netPay     = $posting->net_amount;
                            @endphp
                                <tr>
                                    <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                    <td><span class="font-monospace small">{{ $posting->reference_number }}</span></td>
                                    <td>
                                        <div class="fw-medium">{{ optional($posting->user)->name ?? '-' }}</div>
                                        @if($posting->wallet_address)
                                            <div class="text-muted small font-monospace" title="{{ $posting->wallet_address }}">
                                                {{ substr($posting->wallet_address, 0, 8) }}&hellip;{{ substr($posting->wallet_address, -4) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if($posting->date_from && $posting->date_to)
                                            {{ \Carbon\Carbon::parse($posting->date_from)->format('M d') }}
                                            &ndash;
                                            {{ \Carbon\Carbon::parse($posting->date_to)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($posting->total_hours, 2) }}</td>
                                    <td class="text-end">₱ {{ number_format($posting->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        @if($adjAdds > 0)
                                            <span class="text-success small d-block">+₱{{ number_format($adjAdds,2) }}</span>
                                        @endif
                                        @if($adjDeducts > 0)
                                            <span class="text-danger small d-block">-₱{{ number_format($adjDeducts,2) }}</span>
                                        @endif
                                        @if($adjAdds == 0 && $adjDeducts == 0)
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-primary">₱ {{ number_format($netPay, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $posting->status }} px-2 py-1">{{ $posting->status }}</span>
                                    </td>
                                    <td class="text-nowrap small">
                                        {{ $posting->approved_at ? \Carbon\Carbon::parse($posting->approved_at)->format('M d, Y H:i') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('Payslip.view', $posting->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ri-file-text-line me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4 text-muted">
                                        <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                                        No payment records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($paymentPostings->count())
                        <tfoot class="table-light fw-semibold">
                            <tr>
                                <td colspan="4" class="text-end">Totals</td>
                                <td class="text-end">{{ number_format($totalHours, 2) }}</td>
                                <td class="text-end">₱ {{ number_format($totalAmount, 2) }}</td>
                                <td class="text-end small">
                                    <span class="text-success d-block">+₱{{ number_format($paymentPostings->sum(fn($p)=>$p->adjustments->where('type','add')->sum('amount')),2) }}</span>
                                    <span class="text-danger d-block">-₱{{ number_format($paymentPostings->sum(fn($p)=>$p->adjustments->where('type','deduct')->sum('amount')),2) }}</span>
                                </td>
                                <td class="text-end text-primary">₱ {{ number_format($totalNet, 2) }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });
    });
</script>
@endsection
