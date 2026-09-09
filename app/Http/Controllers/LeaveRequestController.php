<?php

namespace App\Http\Controllers;

use App\LeaveRequest;
use App\Project;
use App\User;
use App\WorkspaceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isReviewer = $user->role === 'Admin'
            || ($user->role === 'Project Lead' && $this->projectLeadsCanReview());
        $myRequests = LeaveRequest::with('reviewer')
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        $reviewRequests = collect();
        if ($isReviewer) {
            $query = LeaveRequest::with(['user', 'reviewer'])
                ->where('user_id', '!=', $user->id)
                ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
                ->orderBy('start_date', 'asc');

            if ($user->role === 'Project Lead') {
                $projectIds = Project::whereHas('users', function ($projectQuery) use ($user) {
                    $projectQuery->where('users.id', $user->id);
                })->pluck('id');

                $reviewableUserIds = User::whereHas('projects', function ($projectQuery) use ($projectIds) {
                    $projectQuery->whereIn('projects.id', $projectIds);
                })->pluck('id');

                $query->whereIn('user_id', $reviewableUserIds);
            }

            $reviewRequests = $query->get();
        }

        return view('leave.index', compact('myRequests', 'reviewRequests', 'isReviewer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:Vacation,Sick,Emergency,Personal,Other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $overlap = LeaveRequest::where('user_id', auth()->id())
            ->whereIn('status', ['Pending', 'Approved'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($rangeQuery) use ($validated) {
                        $rangeQuery->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'A pending or approved leave request already covers these dates.'])->withInput();
        }

        LeaveRequest::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'status' => 'Pending',
        ]));

        return back()->with('success', 'Leave request submitted for review.');
    }

    public function review(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $this->authorizeReviewer($leaveRequest);

        $validated = $request->validate([
            'status' => 'required|in:Approved,Declined',
            'review_note' => 'nullable|string|max:500',
        ]);

        if ($leaveRequest->status !== 'Pending') {
            return back()->with('error', 'Only pending requests can be reviewed.');
        }

        $leaveRequest->update([
            'status' => $validated['status'],
            'review_note' => $validated['review_note'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Leave request '.strtolower($validated['status']).'.');
    }

    public function cancel($id)
    {
        $leaveRequest = LeaveRequest::where('user_id', auth()->id())->findOrFail($id);

        if ($leaveRequest->status !== 'Pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $leaveRequest->update(['status' => 'Cancelled']);

        return back()->with('success', 'Leave request cancelled.');
    }

    private function authorizeReviewer(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        abort_unless(
            $user->role === 'Admin' || ($user->role === 'Project Lead' && $this->projectLeadsCanReview()),
            403
        );

        if ($user->role === 'Project Lead') {
            $projectIds = Project::whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->pluck('id');

            $canReview = $leaveRequest->user->projects()->whereIn('projects.id', $projectIds)->exists();
            abort_unless($canReview, 403);
        }
    }

    private function projectLeadsCanReview()
    {
        if (!Schema::hasTable('workspace_settings')) {
            return true;
        }

        $settings = WorkspaceSetting::first();

        return !$settings || $settings->leave_approval_role === 'Project Lead';
    }
}
