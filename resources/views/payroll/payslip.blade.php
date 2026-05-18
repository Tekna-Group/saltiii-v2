@extends('layouts.header')
@section('css')
<style>
    @media print {
        body * { visibility: hidden; }
        #payslip-wrapper, #payslip-wrapper * { visibility: visible; }
        #payslip-wrapper { position: absolute; inset: 0; }
        .no-print { display: none !important; }
        .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
    }
    #payslip-wrapper { max-width: 860px; margin: 0 auto; }
    .payslip-header { border-bottom: 3px solid #405189; padding-bottom: 16px; margin-bottom: 20px; }
    .info-table td { padding: 4px 8px; vertical-align: top; }
    .info-table td:first-child { font-weight: 600; width: 150px; color: #495057; }
    .hours-table thead th { background: #405189; color: #fff; }
    .hours-table tfoot td { background: #f3f6f9; font-weight: 700; }
    .summary-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 16px 20px; }
    .summary-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed #dee2e6; }
    .summary-row:last-child { border: none; font-weight: 700; font-size: 1.1em; }
    .summary-row.add-row  { color: #1a9b5f; }
    .summary-row.ded-row  { color: #c0392b; }
    .summary-row.net-row  { color: #405189; border-top: 2px solid #405189; margin-top: 4px; padding-top: 8px; }
    .status-Completed { background:#d1f2eb; color:#1a9b5f; }
    .status-Approved  { background:#fef9e7; color:#d68910; }
    .status-badge { display:inline-block; padding:3px 14px; border-radius:20px; font-size:.85em; font-weight:600; }
    .sig-line { border-top: 1px solid #adb5bd; width: 200px; margin-top: 40px; }
    .adj-table td, .adj-table th { padding: 6px 10px; font-size: .87rem; }
</style>
@endsection
@section('content')

<div class="no-print mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
        <i class="ri-arrow-left-line me-1"></i>Back
    </a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        <i class="ri-printer-line me-1"></i>Print Payslip
    </button>
</div>

<div class="row g-4">

    {{-- ===== LEFT: PAYSLIP DOCUMENT ===== --}}
    <div class="col-xl-8" id="payslip-wrapper">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                {{-- Header --}}
                <div class="payslip-header d-flex justify-content-between align-items-start">
                    <div>
                        <img src="{{ asset('images/Saltiii-Logo-White.svg') }}" style="height:44px;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="display:none; font-size:1.5em; font-weight:700; color:#405189;">SALTIII</div>
                        <div class="text-muted small mt-1">Payslip / Proof of Payment</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold fs-5 text-primary">{{ $posting->reference_number }}</div>
                        <div class="small text-muted">Reference Number</div>
                        <span class="status-badge status-{{ $posting->status }} mt-1 d-inline-block">{{ $posting->status }}</span>
                    </div>
                </div>

                {{-- Employee & Payment Info --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h6 class="section-label">Employee Information</h6>
                        <table class="info-table w-100">
                            <tr><td>Name</td><td>{{ optional($posting->user)->name ?? '—' }}</td></tr>
                            <tr><td>Email</td><td>{{ optional($posting->user)->email ?? '—' }}</td></tr>
                            <tr><td>Hourly Rate</td><td>₱ {{ number_format(optional(optional($posting->user)->salary)->salary ?? 0, 2) }}</td></tr>
                            @if($posting->wallet_address)
                            <tr><td>Wallet</td><td class="font-monospace small" style="word-break:break-all;">{{ $posting->wallet_address }}</td></tr>
                            <tr><td>Network</td><td>{{ $posting->wallet_network ?? 'Solana' }}</td></tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="section-label">Payment Details</h6>
                        <table class="info-table w-100">
                            <tr>
                                <td>Pay Period</td>
                                <td>
                                    @if($posting->date_from && $posting->date_to)
                                        {{ \Carbon\Carbon::parse($posting->date_from)->format('M d, Y') }} &mdash;
                                        {{ \Carbon\Carbon::parse($posting->date_to)->format('M d, Y') }}
                                    @else —
                                    @endif
                                </td>
                            </tr>
                            <tr><td>Method</td><td>{{ $posting->payment_method ?? 'USDC (Solana)' }}</td></tr>
                            <tr><td>Approved By</td><td>{{ $posting->payment_approved_by ?? '—' }}</td></tr>
                            <tr><td>Approved At</td><td>{{ $posting->approved_at ? \Carbon\Carbon::parse($posting->approved_at)->format('M d, Y H:i') : '—' }}</td></tr>
                            @if($posting->notes)
                            <tr><td>Notes</td><td class="small text-muted">{{ $posting->notes }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Activity Breakdown --}}
                @php
                    $activities     = $posting->activities->sortBy('date');
                    $groupedByDate  = $activities->groupBy(fn($a) => optional($a->date)->format('Y-m-d'));
                    $hourlyRate     = optional(optional($posting->user)->salary)->salary ?? 0;
                    $adjustments    = $posting->adjustments;
                    $totalAdds      = $adjustments->where('type','add')->sum('amount');
                    $totalDeducts   = $adjustments->where('type','deduct')->sum('amount');
                    $netAmount      = $posting->total_amount + $totalAdds - $totalDeducts;
                @endphp

                @if($activities->count())
                <h6 class="section-label">Activity Breakdown</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered hours-table align-middle mb-0" style="font-size:.87rem;">
                        <thead>
                            <tr>
                                <th>Date</th><th>Project</th><th>Task</th><th>Activity</th>
                                <th class="text-end">Hours</th><th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedByDate as $date => $dayActivities)
                                @foreach($dayActivities as $j => $act)
                                <tr>
                                    @if($j === 0)
                                    <td rowspan="{{ $dayActivities->count() }}" class="text-nowrap text-center align-middle">
                                        <div class="fw-medium">{{ \Carbon\Carbon::parse($date)->format('M d') }}</div>
                                        <div class="text-muted small">{{ \Carbon\Carbon::parse($date)->format('D') }}</div>
                                    </td>
                                    @endif
                                    <td>{{ optional($act->project)->name ?? '—' }}</td>
                                    <td>{{ optional($act->task)->title ?? optional($act->task)->name ?? '—' }}</td>
                                    <td>{{ $act->activity ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($act->hours, 2) }}</td>
                                    <td class="text-end">₱ {{ number_format($act->hours * $hourlyRate, 2) }}</td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end">Subtotal</td>
                                <td class="text-end">{{ number_format($posting->total_hours, 2) }} hrs</td>
                                <td class="text-end">₱ {{ number_format($posting->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-light border mb-4 small text-muted">No activity details linked to this payslip.</div>
                @endif

                {{-- Pay Summary --}}
                <div class="row justify-content-end mb-4">
                    <div class="col-md-5">
                        <div class="summary-box">
                            <h6 class="section-label mb-3">Pay Summary</h6>
                            <div class="summary-row">
                                <span>Total Hours</span>
                                <span>{{ number_format($posting->total_hours, 2) }} hrs</span>
                            </div>
                            <div class="summary-row">
                                <span>Hourly Rate</span>
                                <span>₱ {{ number_format($hourlyRate, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Gross Pay</span>
                                <span>₱ {{ number_format($posting->total_amount, 2) }}</span>
                            </div>
                            @foreach($adjustments->where('type','add') as $adj)
                            <div class="summary-row add-row">
                                <span><i class="ri-add-circle-line me-1"></i>{{ $adj->description }}</span>
                                <span>+ ₱ {{ number_format($adj->amount, 2) }}</span>
                            </div>
                            @endforeach
                            @foreach($adjustments->where('type','deduct') as $adj)
                            <div class="summary-row ded-row">
                                <span><i class="ri-indeterminate-circle-line me-1"></i>{{ $adj->description }}</span>
                                <span>- ₱ {{ number_format($adj->amount, 2) }}</span>
                            </div>
                            @endforeach
                            @php $usdcMatch = null; if($posting->notes && preg_match('/USDC Transfer Amount:\s*([\d.]+)/', $posting->notes, $m)) $usdcMatch = $m[1]; @endphp
                            @if($usdcMatch)
                            <div class="summary-row">
                                <span>USDC Transferred</span>
                                <span>{{ number_format($usdcMatch, 4) }} USDC</span>
                            </div>
                            @endif
                            <div class="summary-row net-row">
                                <span>Net Pay</span>
                                <span>₱ {{ number_format($netAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Signatures --}}
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="sig-line"></div>
                        <div class="small text-muted mt-1">Employee Signature</div>
                        <div class="fw-medium">{{ optional($posting->user)->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 offset-md-4 text-end">
                        <div class="sig-line ms-auto"></div>
                        <div class="small text-muted mt-1">Authorized Signatory</div>
                        <div class="fw-medium">{{ $posting->payment_approved_by ?? '—' }}</div>
                    </div>
                </div>

                <hr class="mt-4">
                <div class="text-center text-muted small">
                    Generated on {{ now()->format('F d, Y \a\t H:i') }} &bull; {{ $posting->reference_number }} &bull; System-generated payslip.
                </div>

            </div>
        </div>
    </div>

    {{-- ===== RIGHT: ADJUSTMENT PANEL (Admin/Timekeeper only) ===== --}}
    @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Timekeeper')
    <div class="col-xl-4 no-print">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="ri-scales-3-line me-1 text-warning"></i>Payroll Adjustments</h6>
            </div>
            <div class="card-body">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible py-2 small fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible py-2 small fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- Existing adjustments --}}
                @if($adjustments->count())
                <table class="table table-sm table-bordered adj-table mb-3">
                    <thead class="table-light">
                        <tr><th>Type</th><th>Description</th><th class="text-end">Amount</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($adjustments as $adj)
                        <tr>
                            <td>
                                @if($adj->type === 'add')
                                    <span class="badge bg-success-subtle text-success">+ Add</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">- Deduct</span>
                                @endif
                            </td>
                            <td>{{ $adj->description }}</td>
                            <td class="text-end fw-medium">₱ {{ number_format($adj->amount, 2) }}</td>
                            <td class="text-center">
                                @if($posting->status !== 'Completed')
                                <form method="POST" action="{{ route('Payslip.adjustment.destroy', $adj->id) }}"
                                      onsubmit="return confirm('Remove this adjustment?')">
                                    @csrf
                                    <button class="btn btn-sm btn-link text-danger p-0">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                @else
                                    <i class="ri-lock-line text-muted"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-semibold">Net Pay</td>
                            <td class="text-end fw-bold text-primary">₱ {{ number_format($netAmount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                @else
                <p class="text-muted small mb-3">No adjustments added yet.</p>
                @endif

                {{-- Add adjustment form --}}
                @if($posting->status === 'Completed')
                    <div class="alert alert-secondary py-2 small mb-0">
                        <i class="ri-lock-line me-1"></i>Adjustments are locked — this payment has been <strong>Completed</strong>.
                    </div>
                @else
                <form method="POST" action="{{ route('Payslip.adjustment.store', $posting->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label mb-1 small fw-semibold">Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_add" value="add" checked>
                                <label class="form-check-label text-success fw-medium" for="type_add">
                                    <i class="ri-add-circle-line me-1"></i>Add
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_deduct" value="deduct">
                                <label class="form-check-label text-danger fw-medium" for="type_deduct">
                                    <i class="ri-indeterminate-circle-line me-1"></i>Deduct
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1 small fw-semibold">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror"
                               placeholder="e.g. Bonus, Late penalty, Allowance…"
                               value="{{ old('description') }}" required>
                        @error('description')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1 small fw-semibold">Amount (₱)</label>
                        <input type="number" name="amount" step="0.01" min="0.01"
                               class="form-control form-control-sm @error('amount') is-invalid @enderror"
                               placeholder="0.00"
                               value="{{ old('amount') }}" required>
                        @error('amount')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="ri-add-line me-1"></i>Add Adjustment
                    </button>
                </form>
                @endif

            </div>
        </div>

        {{-- Quick summary card --}}
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title mb-3 small text-muted text-uppercase fw-semibold" style="letter-spacing:.05em;">Pay Breakdown</h6>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Gross Pay</span>
                    <span class="small">₱ {{ number_format($posting->total_amount, 2) }}</span>
                </div>
                @if($totalAdds > 0)
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-success small">Total Additions</span>
                    <span class="text-success small">+ ₱ {{ number_format($totalAdds, 2) }}</span>
                </div>
                @endif
                @if($totalDeducts > 0)
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-danger small">Total Deductions</span>
                    <span class="text-danger small">- ₱ {{ number_format($totalDeducts, 2) }}</span>
                </div>
                @endif
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold">Net Pay</span>
                    <span class="fw-bold text-primary fs-6">₱ {{ number_format($netAmount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@endsection
@section('js')
<style>
    .section-label {
        text-transform: uppercase;
        letter-spacing: .05em;
        font-size: .72rem;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: .5rem;
    }
</style>
@endsection
