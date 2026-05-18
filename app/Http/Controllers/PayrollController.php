<?php

namespace App\Http\Controllers;

use App\PaymentPosting;
use App\PayrollAdjustment;
use App\User;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentPosting::with(['user', 'user.salary', 'adjustments']);

        if ($request->date_from) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('date_from', '>=', $request->date_from)
                  ->orWhereDate('approved_at', '>=', $request->date_from);
            });
        }
        if ($request->date_to) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('date_to', '<=', $request->date_to)
                  ->orWhereDate('approved_at', '<=', $request->date_to);
            });
        }
        if ($request->user_id && $request->user_id !== 'ALL') {
            $query->where('user_id', $request->user_id);
        }
        if ($request->status && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        $paymentPostings = $query->orderBy('approved_at', 'desc')->get();
        $users = User::orderBy('name')->get();

        $totalHours   = $paymentPostings->sum('total_hours');
        $totalAmount  = $paymentPostings->sum('total_amount');
        $totalNet     = $paymentPostings->sum(fn($p) => $p->net_amount);
        $totalPaid    = $paymentPostings->where('status', 'Completed')->sum(fn($p) => $p->net_amount);
        $totalPending = $paymentPostings->where('status', 'Approved')->sum(fn($p) => $p->net_amount);

        return view('payroll.index', compact(
            'paymentPostings', 'users', 'totalHours', 'totalAmount', 'totalNet', 'totalPaid', 'totalPending'
        ));
    }

    public function myPayslips(Request $request)
    {
        $paymentPostings = PaymentPosting::with(['adjustments'])
            ->where('user_id', auth()->id())
            ->orderBy('approved_at', 'desc')
            ->get();

        $totalHours  = $paymentPostings->sum('total_hours');
        $totalNet    = $paymentPostings->sum(fn($p) => $p->net_amount);

        return view('payroll.my_payslips', compact('paymentPostings', 'totalHours', 'totalNet'));
    }

    public function payslip($id)
    {
        $posting = PaymentPosting::with([
            'user',
            'user.salary',
            'adjustments',
            'activities',
            'activities.task',
            'activities.project',
        ])->findOrFail($id);

        // Employees can only see their own payslips
        if (auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Timekeeper') {
            abort_unless($posting->user_id === auth()->id(), 403);
        }

        return view('payroll.payslip', compact('posting'));
    }

    public function storeAdjustment(Request $request, $postingId)
    {
        $request->validate([
            'type'        => 'required|in:add,deduct',
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        $posting = PaymentPosting::findOrFail($postingId);

        if ($posting->status === 'Completed') {
            return back()->with('error', 'Adjustments cannot be added once payment is completed.');
        }

        PayrollAdjustment::create([
            'payment_posting_id' => $posting->id,
            'type'               => $request->type,
            'description'        => $request->description,
            'amount'             => $request->amount,
            'created_by'         => auth()->user()->name,
        ]);

        return back()->with('success', 'Adjustment added successfully.');
    }

    public function destroyAdjustment($id)
    {
        $adjustment = PayrollAdjustment::with('paymentPosting')->findOrFail($id);

        if (optional($adjustment->paymentPosting)->status === 'Completed') {
            return back()->with('error', 'Adjustments cannot be removed once payment is completed.');
        }

        $adjustment->delete();

        return back()->with('success', 'Adjustment removed.');
    }
}
