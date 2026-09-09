@extends('layouts.header')

@section('title', 'Leave | SALTiii')

@section('css')
<link href="{{ asset('inside_css/assets/css/saltiii-operations.css') }}" rel="stylesheet" />
@endsection

@section('content')
@php
    $pendingMine = $myRequests->where('status', 'Pending');
    $approvedMine = $myRequests->where('status', 'Approved');
    $pendingReviews = $reviewRequests->where('status', 'Pending');
    $approvedDays = $approvedMine->sum(function ($leave) { return $leave->start_date->diffInDays($leave->end_date) + 1; });
@endphp

<div class="operations-page leave-page">
    <header class="operations-header"><div><span class="operations-eyebrow">Leave and availability</span><h1>Time away, clearly coordinated.</h1><p>Submit leave dates, track decisions, and review team requests in one place.</p></div><button type="button" class="btn operations-primary" data-bs-toggle="modal" data-bs-target="#leaveRequestModal"><i class="ri-add-line" aria-hidden="true"></i> Request leave</button></header>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Please review the request.</strong> {{ $errors->first() }}</div>@endif

    <section class="operations-signal-strip" aria-label="Leave summary">
        <div><small>My pending</small><strong>{{ $pendingMine->count() }}</strong><span>Waiting for review</span></div>
        <div><small>Approved days</small><strong>{{ $approvedDays }}</strong><span>Across recorded requests</span></div>
        <div><small>My requests</small><strong>{{ $myRequests->count() }}</strong><span>Complete history</span></div>
        @if($isReviewer)<div><small>Team decisions</small><strong>{{ $pendingReviews->count() }}</strong><span>Pending your review</span></div>@else<div><small>Next approved leave</small><strong>{{ optional($approvedMine->where('end_date', '>=', now())->sortBy('start_date')->first())->start_date ? optional($approvedMine->where('end_date', '>=', now())->sortBy('start_date')->first())->start_date->format('M j') : '—' }}</strong><span>Upcoming time away</span></div>@endif
    </section>

    @if($isReviewer)
    <section class="leave-approval-queue" aria-labelledby="leave-approval-title">
        <header class="operations-section-head"><div><span class="operations-eyebrow">Manager queue</span><h2 id="leave-approval-title">Requests to review</h2></div><span>{{ $pendingReviews->count() }} pending</span></header>
        <div class="leave-request-list">
            @forelse($reviewRequests as $leave)
                <article class="leave-request-row {{ $leave->status !== 'Pending' ? 'is-reviewed' : '' }}"><span class="leave-avatar">{{ strtoupper(substr($leave->user->name, 0, 1)) }}</span><div class="leave-person"><strong>{{ $leave->user->name }}</strong><small>{{ $leave->leave_type }} leave</small></div><div class="leave-dates"><strong>{{ $leave->start_date->format('M j') }}–{{ $leave->end_date->format('M j, Y') }}</strong><small>{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} calendar {{ str_plural('day', $leave->start_date->diffInDays($leave->end_date) + 1) }}</small></div><p>{{ $leave->reason }}</p><span class="leave-status status-{{ strtolower($leave->status) }}">{{ $leave->status }}</span>
                    @if($leave->status === 'Pending')<div class="leave-review-actions"><button type="button" class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#reviewLeave{{ $leave->id }}">Review</button></div>@else<div class="leave-reviewer">{{ optional($leave->reviewer)->name ?: 'Reviewed' }}</div>@endif
                </article>
                @if($leave->status === 'Pending')<div class="modal fade" id="reviewLeave{{ $leave->id }}" tabindex="-1" aria-labelledby="reviewLeaveTitle{{ $leave->id }}" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="POST" action="{{ route('leave.review', $leave->id) }}">@csrf<div class="modal-header"><div><h5 class="modal-title" id="reviewLeaveTitle{{ $leave->id }}">Review {{ $leave->user->name }}’s request</h5><p class="text-muted mb-0 mt-1 fs-12">{{ $leave->start_date->format('M j') }}–{{ $leave->end_date->format('M j, Y') }} · {{ $leave->leave_type }}</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><label class="form-label" for="reviewNote{{ $leave->id }}">Review note <small class="text-muted fw-normal">Optional</small></label><textarea class="form-control" id="reviewNote{{ $leave->id }}" name="review_note" rows="3" maxlength="500" placeholder="Add context for the employee"></textarea></div><div class="modal-footer"><button class="btn btn-outline-danger" type="submit" name="status" value="Declined">Decline</button><button class="btn btn-primary" type="submit" name="status" value="Approved">Approve leave</button></div></form></div></div></div>@endif
            @empty
                <div class="operations-empty"><i class="ri-calendar-check-line" aria-hidden="true"></i><div><strong>No team requests yet</strong><p>New requests from eligible team members will appear here.</p></div></div>
            @endforelse
        </div>
    </section>
    @endif

    <section class="operations-register" aria-labelledby="my-leave-title"><header class="operations-section-head"><div><span class="operations-eyebrow">Personal history</span><h2 id="my-leave-title">My leave requests</h2></div><span>{{ $myRequests->count() }} records</span></header><div class="operations-table-wrap"><table class="operations-table leave-history-table"><thead><tr><th>Type</th><th>Dates</th><th>Duration</th><th>Status</th><th>Reviewed by</th><th>Action</th></tr></thead><tbody>
        @forelse($myRequests as $leave)<tr><td><strong>{{ $leave->leave_type }}</strong><small>{{ Str::limit($leave->reason, 54) }}</small></td><td>{{ $leave->start_date->format('M j') }}–{{ $leave->end_date->format('M j, Y') }}</td><td>{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} days</td><td><span class="leave-status status-{{ strtolower($leave->status) }}">{{ $leave->status }}</span></td><td>{{ optional($leave->reviewer)->name ?: '—' }}@if($leave->review_note)<small>{{ $leave->review_note }}</small>@endif</td><td>@if($leave->status === 'Pending')<form method="POST" action="{{ route('leave.cancel', $leave->id) }}" onsubmit="return confirm('Cancel this leave request?')">@csrf<button type="submit" class="btn btn-sm btn-light">Cancel</button></form>@else<span class="text-muted">—</span>@endif</td></tr>
        @empty<tr><td colspan="6"><div class="operations-empty"><i class="ri-calendar-event-line" aria-hidden="true"></i><div><strong>No leave requests</strong><p>Your request history will appear here.</p></div><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leaveRequestModal">Request leave</button></div></td></tr>@endforelse
    </tbody></table></div></section>
</div>

<div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-labelledby="leaveRequestTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="POST" action="{{ route('leave.store') }}">@csrf<div class="modal-header"><div><h5 class="modal-title" id="leaveRequestTitle">Request leave</h5><p class="text-muted mb-0 mt-1 fs-12">Provide the dates and context your reviewer needs.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label" for="leaveType">Leave type</label><select class="form-select" id="leaveType" name="leave_type" required><option value="">Choose a type</option>@foreach(['Vacation','Sick','Emergency','Personal','Other'] as $type)<option value="{{ $type }}" {{ old('leave_type') === $type ? 'selected' : '' }}>{{ $type }}</option>@endforeach</select></div><div class="row g-3 mb-3"><div class="col-sm-6"><label class="form-label" for="leaveStart">Start date</label><input class="form-control" id="leaveStart" type="date" name="start_date" min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}" required></div><div class="col-sm-6"><label class="form-label" for="leaveEnd">End date</label><input class="form-control" id="leaveEnd" type="date" name="end_date" min="{{ date('Y-m-d') }}" value="{{ old('end_date') }}" required></div></div><div><label class="form-label" for="leaveReason">Reason</label><textarea class="form-control" id="leaveReason" name="reason" rows="4" maxlength="1000" placeholder="Briefly explain the request" required>{{ old('reason') }}</textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Submit request</button></div></form></div></div></div>
@endsection

@section('js')
<script>document.getElementById('leaveStart').addEventListener('change',function(){var end=document.getElementById('leaveEnd');end.min=this.value;if(end.value&&end.value<this.value)end.value=this.value;});@if($errors->any())document.addEventListener('DOMContentLoaded',function(){new bootstrap.Modal(document.getElementById('leaveRequestModal')).show();});@endif</script>
@endsection
