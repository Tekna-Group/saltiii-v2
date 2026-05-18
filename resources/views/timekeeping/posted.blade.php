@extends('layouts.header')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12 col-md-12">
        <div class='card'>
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Posted Timekeeping Report</h5>
                <a href="{{ route('Timekeeping') }}" class="btn btn-secondary btn-sm">Back to Timekeeping</a>
            </div>
            <form method="GET" action="{{ route('Timekeeping.posted') }}">
                <div class='card-body'>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="row gy-3">
                        <div class="col-xl-3 col-md-4">
                            <label class="form-label">Members</label>
                            <select class='form-control select2' name='members'>
                                <option value='ALL' @if($selected_member == 'ALL') selected @endif>ALL</option>
                                @foreach($members as $member)
                                    <option value='{{ $member->id }}' @if($selected_member == $member->id) selected @endif>{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-4">
                            <label class="form-label">Posted From</label>
                            <input type='date' name='date_from' value='{{ $date_from }}' class='form-control form-control-sm'>
                        </div>
                        <div class="col-xl-3 col-md-4">
                            <label class="form-label">Posted To</label>
                            <input type='date' name='date_to' value='{{ $date_to }}' class='form-control form-control-sm'>
                        </div>
                        <div class="col-xl-3 col-md-12 d-flex align-items-end">
                            <button type="submit" class="btn btn-success">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-xl-12">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Timekeeping Summary</h5>
                <div>
                    <button type="button" class="btn btn-warning btn-sm me-2" id="select-all-btn" style="display: none;">
                        <i class="fas fa-check-square me-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="bulk-transfer-btn" style="display: none;" data-bs-toggle="modal" data-bs-target="#bulkUsdcTransferModal">
                        <i class="fas fa-exchange-alt me-1"></i>Transfer Selected (<span id="selected-count">0</span>)
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @php
                        $groupedActivities = $activities->groupBy('user_id');
                    @endphp
                    <table class="table table-bordered table-nowrap table-centered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" id="select-all-checkbox">
                                </th>
                                <th>User</th>
                                <th>Hourly Rate</th>
                                <th>Posted From</th>
                                <th>Posted To</th>
                                @foreach($date_ranges as $date)
                                    <th>{{ date('d M', strtotime($date)) }}</th>
                                @endforeach
                                <th>Total Hours</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Adjustments</th>
                                <th>Net Pay</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupedActivities as $userId => $rows)
                                @php
                                    $user = $rows->first()->user;
                                    $hourlyRate = optional($user->salary)->salary ?? 0;
                                    $postedFrom = $rows->min('timekeeping_from');
                                    $postedTo = $rows->max('timekeeping_to');
                                    $totalHours = 0;
                                    $paymentStatus = $rows->every(function($activity){ return !empty($activity->payment_reference); }) ? 'Approved' : 'Pending';

                                    foreach($date_ranges as $date) {
                                        $dayHours = $rows->filter(function ($activity) use ($date) {
                                            return optional($activity->date)->format('Y-m-d') === $date;
                                        })->sum('hours');
                                        $totalHours += $dayHours;
                                    }

                                    $grossPay = $totalHours * $hourlyRate;
                                    $posting  = $paymentPostings->get($userId);
                                    $adjAdds    = $posting ? $posting->adjustments->where('type','add')->sum('amount') : 0;
                                    $adjDeducts = $posting ? $posting->adjustments->where('type','deduct')->sum('amount') : 0;
                                    $netPay   = $grossPay + $adjAdds - $adjDeducts;
                                @endphp
                                <tr class="user-row" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-hourly-rate="{{ $hourlyRate }}" data-date-from="{{ $postedFrom }}" data-date-to="{{ $postedTo }}" data-total-hours="{{ $totalHours }}" data-total-amount="{{ $grossPay }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}" @if($paymentStatus === 'Approved') disabled @endif>
                                    </td>
                                    <td>{{ optional($user)->name }}</td>
                                    <td>{{ number_format($hourlyRate, 2) }}</td>
                                    <td>{{ $postedFrom }}</td>
                                    <td>{{ $postedTo }}</td>
                                    @foreach($date_ranges as $date)
                                        @php
                                            $dayHours = $rows->filter(function ($activity) use ($date) {
                                                return optional($activity->date)->format('Y-m-d') === $date;
                                            })->sum('hours');
                                        @endphp
                                        <td>{{ number_format($dayHours, 2) }}</td>
                                    @endforeach
                                    <td>{{ number_format($totalHours, 2) }}</td>
                                    <td>{{ number_format($grossPay, 2) }}</td>
                                    <td>
                                        @if($paymentStatus === 'Approved')
                                            <span class="text-success small d-block">+₱{{ number_format($adjAdds, 2) }}</span>
                                            <span class="text-danger small d-block">-₱{{ number_format($adjDeducts, 2) }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold {{ $paymentStatus === 'Approved' ? 'text-primary' : '' }}">
                                        {{ $paymentStatus === 'Approved' ? '₱ '.number_format($netPay, 2) : '—' }}
                                    </td>
                                    <td style="white-space:nowrap;">
                                        @if($posting && $posting->status === 'Completed')
                                            {{-- USDC transfer done, everything locked --}}
                                            <button class="btn btn-sm btn-secondary me-1" disabled>
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Locked after USDC transfer">
                                                <i class="ri-lock-line me-1"></i>Locked
                                            </button>
                                        @elseif($posting && $posting->status === 'Approved')
                                            {{-- Approved: add adjustments then transfer --}}
                                            <button type="button" class="btn btn-sm btn-success me-1"
                                                data-bs-toggle="modal" data-bs-target="#usdcTransferModal"
                                                data-user-id="{{ $user->id }}"
                                                data-date-from="{{ $date_from }}"
                                                data-date-to="{{ $date_to }}"
                                                data-members="{{ $selected_member }}">
                                                <i class="fas fa-exchange-alt me-1"></i>Transfer USDC
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning btn-adjustment"
                                                data-bs-toggle="modal" data-bs-target="#adjustmentModal"
                                                data-posting-id="{{ $posting->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-gross="{{ number_format($grossPay, 2) }}"
                                                data-adjustments="{{ json_encode($posting->adjustments->map(fn($a) => ['id'=>$a->id,'type'=>$a->type,'description'=>$a->description,'amount'=>$a->amount])) }}">
                                                <i class="ri-scales-3-line me-1"></i>Adjustment
                                            </button>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 4 + count($date_ranges) + 4 }}" class="text-center">No posted timekeeping found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($groupedActivities->count())
                        <tfoot>
                            <tr>
                                <td colspan="{{ 4 + count($date_ranges) }}" class="text-end fw-semibold">Totals</td>
                                <td class="fw-semibold">{{ number_format($activities->sum('hours'), 2) }}</td>
                                <td class="fw-semibold">{{ number_format($activities->sum(function($activity){ return $activity->hours * (optional($activity->user->salary)->salary ?? 0); }), 2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Adjustment Modal --}}
<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title"><i class="ri-scales-3-line me-2 text-warning"></i>Payroll Adjustment — <span id="adj-user-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Existing adjustments --}}
                <div id="adj-existing-section">
                    <h6 class="text-muted text-uppercase fw-semibold mb-2" style="font-size:.75rem; letter-spacing:.05em;">Existing Adjustments</h6>
                    <table class="table table-sm table-bordered mb-3" id="adj-list-table">
                        <thead class="table-light">
                            <tr><th>Type</th><th>Description</th><th class="text-end">Amount</th><th></th></tr>
                        </thead>
                        <tbody id="adj-list-body">
                            <tr id="adj-empty-row"><td colspan="4" class="text-center text-muted small">No adjustments yet.</td></tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end fw-semibold">Net Pay</td>
                                <td class="text-end fw-bold text-primary" id="adj-net-pay"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Add new adjustment --}}
                <h6 class="text-muted text-uppercase fw-semibold mb-2" style="font-size:.75rem; letter-spacing:.05em;">Add Adjustment</h6>
                <form id="adj-form" method="POST" action="">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label mb-1 small fw-semibold">Type</label>
                            <select name="type" id="adj-type" class="form-select form-select-sm" required>
                                <option value="add">+ Add</option>
                                <option value="deduct">- Deduct</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label mb-1 small fw-semibold">Description</label>
                            <input type="text" name="description" id="adj-description" class="form-control form-control-sm"
                                   placeholder="e.g. Bonus, Late penalty, Allowance…" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small fw-semibold">Amount (₱)</label>
                            <input type="number" name="amount" id="adj-amount" class="form-control form-control-sm"
                                   step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="ri-add-line me-1"></i>Add
                            </button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer bg-light">
                <div class="text-muted small me-auto">Gross Pay: ₱ <span id="adj-gross"></span></div>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@include('timekeeping.partials.usdc-transfer-modal')
@include('timekeeping.partials.bulk-usdc-transfer-modal')

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        initializeBulkTransfer();
        initializeAdjustmentModal();
    });

    function initializeAdjustmentModal() {
        const modal = document.getElementById('adjustmentModal');
        if (!modal) return;

        // Populate modal when opened
        modal.addEventListener('show.bs.modal', function(e) {
            const btn         = e.relatedTarget;
            const postingId   = btn.dataset.postingId;
            const userName    = btn.dataset.userName;
            const gross       = btn.dataset.gross;
            const adjustments = JSON.parse(btn.dataset.adjustments || '[]');

            document.getElementById('adj-user-name').textContent = userName;
            document.getElementById('adj-gross').textContent     = gross;
            document.getElementById('adj-form').action = '/payslips/' + postingId + '/adjustment';

            renderAdjustments(adjustments, parseFloat(gross.replace(/,/g, '')));
        });
    }

    function renderAdjustments(adjustments, gross) {
        const tbody   = document.getElementById('adj-list-body');
        const netEl   = document.getElementById('adj-net-pay');
        const emptyRow = document.getElementById('adj-empty-row');

        tbody.innerHTML = '';

        if (adjustments.length === 0) {
            tbody.innerHTML = '<tr id="adj-empty-row"><td colspan="4" class="text-center text-muted small">No adjustments yet.</td></tr>';
            netEl.textContent = '₱ ' + gross.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
            return;
        }

        let net = gross;
        adjustments.forEach(adj => {
            const amount = parseFloat(adj.amount);
            net += adj.type === 'add' ? amount : -amount;

            const badge  = adj.type === 'add'
                ? '<span class="badge bg-success-subtle text-success">+ Add</span>'
                : '<span class="badge bg-danger-subtle text-danger">- Deduct</span>';
            const sign   = adj.type === 'add' ? '+' : '-';
            const color  = adj.type === 'add' ? 'text-success' : 'text-danger';
            const delUrl = '/payslips/adjustment/' + adj.id + '/delete';

            tbody.innerHTML += `
                <tr>
                    <td>${badge}</td>
                    <td>${adj.description}</td>
                    <td class="text-end fw-medium ${color}">${sign}₱ ${amount.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}</td>
                    <td class="text-center">
                        <form method="POST" action="${delUrl}" onsubmit="return confirm('Remove this adjustment?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button class="btn btn-sm btn-link text-danger p-0"><i class="ri-delete-bin-line"></i></button>
                        </form>
                    </td>
                </tr>`;
        });

        netEl.textContent = '₱ ' + net.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function initializeBulkTransfer() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const selectAllBtn = document.getElementById('select-all-btn');
        const bulkTransferBtn = document.getElementById('bulk-transfer-btn');
        const selectedCountSpan = document.getElementById('selected-count');

        // Select All checkbox
        selectAllCheckbox?.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = this.checked;
                }
            });
            updateBulkActionButtons();
        });

        // Individual checkboxes
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActionButtons);
        });

        // Bulk transfer button
        bulkTransferBtn?.addEventListener('click', function() {
            const selectedUsers = Array.from(userCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => {
                    const row = cb.closest('.user-row');
                    return {
                        user_id: row.dataset.userId,
                        user_name: row.dataset.userName,
                        hourly_rate: row.dataset.hourlyRate,
                        date_from: row.dataset.dateFrom,
                        date_to: row.dataset.dateTo,
                        total_hours: row.dataset.totalHours,
                        total_amount: row.dataset.totalAmount
                    };
                });

            if (selectedUsers.length > 0) {
                window.bulkTransferUsers = selectedUsers;
                displayBulkTransferSummary(selectedUsers);
            }
        });

        function updateBulkActionButtons() {
            const checkedCount = Array.from(userCheckboxes).filter(cb => cb.checked).length;
            selectedCountSpan.textContent = checkedCount;
            
            if (checkedCount > 0) {
                selectAllBtn.style.display = 'inline-block';
                bulkTransferBtn.style.display = 'inline-block';
            } else {
                selectAllBtn.style.display = 'none';
                bulkTransferBtn.style.display = 'none';
            }

            // Update select all checkbox state
            const allChecked = userCheckboxes.length > 0 && 
                Array.from(userCheckboxes).every(cb => !cb.disabled && cb.checked);
            selectAllCheckbox.checked = allChecked;
        }
    }

    function displayBulkTransferSummary(selectedUsers) {
        const summaryContainer = document.getElementById('bulk-transfer-summary');
        if (!summaryContainer) return;

        let totalAmount = 0;
        let totalHours = 0;

        const rows = selectedUsers.map(user => {
            totalAmount += parseFloat(user.total_amount);
            totalHours += parseFloat(user.total_hours);
            return `
                <tr>
                    <td>${user.user_name}</td>
                    <td>${parseFloat(user.total_hours).toFixed(2)} hrs</td>
                    <td>₱${parseFloat(user.total_amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                </tr>
            `;
        }).join('');

        summaryContainer.innerHTML = `
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Hours</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                    <tr class="table-active">
                        <th>Total (${selectedUsers.length} employees)</th>
                        <th>${totalHours.toFixed(2)} hrs</th>
                        <th>₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</th>
                    </tr>
                </tbody>
            </table>
        `;
    }
</script>
@endsection
