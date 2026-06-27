<?php

namespace App\Http\Controllers;
use App\TaskActivity;
use App\User;
use App\Project;
use App\PaymentPosting;
use App\PayrollAdjustment;
use App\TokenTransfer;
use App\Services\ExchangeRateService;
use App\Services\SolanaTokenService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class TimekeepingController extends Controller
{
    //

    public function index(Request $request)
    {
        $date_ranges = [];
        $TaskActivity = collect();
        $projects = collect();
        $members = User::orderBy('name')->get();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $last_sunday = $date_from;
        $saturday = $date_to;
        $users = collect();

        if ($request->date_from && $request->date_to) {
            $activityQuery = TaskActivity::with(['project', 'task', 'user.salary'])
                ->whereBetween('date', [$date_from, $date_to]);
            if ($request->members && $request->members !== 'ALL') {
                $activityQuery->where('user_id', $request->members);
            }
            $TaskActivity = $this->normalizeActivityDates($activityQuery->get());
            $date_ranges = $this->dateRange($date_from, $date_to);
            $projects = Project::whereHas('activities', function ($query) use ($date_from, $date_to, $request) {
                $query->whereBetween('date', [$date_from, $date_to]);
                if ($request->members && $request->members !== 'ALL') {
                    $query->where('user_id', $request->members);
                }
            })->orderBy('name','asc')->get();
            $users = User::whereHas('activities', function ($query) use ($date_from, $date_to, $request) {
                $query->whereBetween('date', [$date_from, $date_to]);
                if ($request->members && $request->members !== 'ALL') {
                    $query->where('user_id', $request->members);
                }
            })->orderBy('name')->get();
            $users->load('salary');
        }

        return view('timekeeping.index', [
            'activities' => $TaskActivity,
            'date_ranges' => $date_ranges,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'users' => $users,
            'last_sunday' => $last_sunday,
            'saturday' => $saturday,
            'projects' => $projects,
            'members' => $members,
        ]);
    }
    public function myTimekeeping(Request $request)
    {
        $date_ranges = [];
        $TaskActivity = collect();
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $last_sunday = $date_from;
        $saturday = $date_to;
        if ($request->date_from && $request->date_to) {
            $TaskActivity = TaskActivity::with(['project', 'task', 'user.salary'])
                ->where('user_id', auth()->user()->id)
                ->whereBetween('date', [$date_from, $date_to])
                ->get();
            $TaskActivity = $this->normalizeActivityDates($TaskActivity);
            $date_ranges = $this->dateRange($date_from, $date_to);
        }
        $users = User::where('id', auth()->user()->id)->with('salary')->get();
        return view('timekeeping.my_timekeeping', [
            'activities' => $TaskActivity,
            'date_ranges' => $date_ranges,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'users' => $users,
            'last_sunday' => $last_sunday,
            'saturday' => $saturday,
        ]);
    }

    public function postTimekeeping(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $query = TaskActivity::with('user.salary')
            ->whereBetween('date', [$request->date_from, $request->date_to]);
        if ($request->members && $request->members !== 'ALL') {
            $query->where('user_id', $request->members);
        }

        $activities = $query->get();

        // Stamp timekeeping period on every activity
        foreach ($activities as $activity) {
            $activity->timekeeping_from = $request->date_from;
            $activity->timekeeping_to   = $request->date_to;
            $activity->generated_by     = auth()->user()->name;
            $activity->save();
        }

        // Create one Approved PaymentPosting per user (skip if one already exists for this range)
        $byUser = $activities->groupBy('user_id');
        foreach ($byUser as $userId => $userActivities) {
            $already = PaymentPosting::where('user_id', $userId)
                ->where('date_from', $request->date_from)
                ->where('date_to', $request->date_to)
                ->exists();

            if ($already) {
                continue;
            }

            $user        = $userActivities->first()->user;
            $totalHours  = $userActivities->sum('hours');
            $totalAmount = $userActivities->sum(function ($a) {
                return $a->hours * (optional($a->user->salary)->salary ?? 0);
            });

            $referenceNumber = 'USDC-' . strtoupper(uniqid());

            $posting = PaymentPosting::create([
                'user_id'             => $userId,
                'reference_number'    => $referenceNumber,
                'wallet_address'      => $user->wallet_address,
                'wallet_network'      => 'Solana',
                'payment_method'      => 'USDC (Solana)',
                'date_from'           => $request->date_from,
                'date_to'             => $request->date_to,
                'total_hours'         => $totalHours,
                'total_amount'        => $totalAmount,
                'status'              => 'Approved',
                'payment_approved_by' => auth()->user()->name,
                'approved_at'         => now(),
            ]);

            // Link activities to this posting
            foreach ($userActivities as $activity) {
                $hourlyRate = optional($activity->user->salary)->salary ?? 0;
                $activity->payment_reference   = $referenceNumber;
                $activity->payment_method      = 'USDC (Solana)';
                $activity->payment_amount      = $activity->hours * $hourlyRate;
                $activity->payment_approved_by = auth()->user()->name;
                $activity->save();
            }
        }

        return redirect()->route('Timekeeping', [
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
            'members'   => $request->members,
        ])->with('success', 'Timekeeping posted successfully.');
    }

    public function postedReport(Request $request)
    {
        $members = User::orderBy('name')->get();

        $query = TaskActivity::with(['user.salary', 'project', 'task'])
            ->whereNotNull('timekeeping_from')
            ->whereNotNull('timekeeping_to');

        if ($request->date_from) {
            $query->whereDate('timekeeping_to', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('timekeeping_from', '<=', $request->date_to);
        }
        if ($request->members && $request->members !== 'ALL') {
            $query->where('user_id', $request->members);
        }

        $activities = $query->orderBy('timekeeping_from', 'desc')->get();

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        if (!$date_from && $activities->count()) {
            $date_from = $activities->min('date');
        }
        if (!$date_to && $activities->count()) {
            $date_to = $activities->max('date');
        }

        $date_ranges = [];
        if ($date_from && $date_to) {
            $date_ranges = $this->dateRange($date_from, $date_to);
        }

        // Load payment postings (with adjustments) keyed by user_id for the Adjustment button
        $references = $activities->pluck('payment_reference')->filter()->unique();
        $paymentPostings = PaymentPosting::with('adjustments')
            ->whereIn('reference_number', $references)
            ->get()
            ->keyBy('user_id');

        return view('timekeeping.posted', [
            'activities'     => $activities,
            'members'        => $members,
            'date_from'      => $date_from,
            'date_to'        => $date_to,
            'selected_member'  => $request->members ?? 'ALL',
            'date_ranges'    => $date_ranges,
            'paymentPostings'=> $paymentPostings,
        ]);
    }

    public function approvePayment(Request $request, $userId)
    {
        $query = TaskActivity::with('user.salary')
            ->where('user_id', $userId)
            ->whereNotNull('timekeeping_from')
            ->whereNotNull('timekeeping_to');

        if ($request->date_from) {
            $query->whereDate('timekeeping_to', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('timekeeping_from', '<=', $request->date_to);
        }

        $activities = $query->get();

        if ($activities->isEmpty()) {
            return redirect()->route('Timekeeping.posted', [
                'members' => $request->members ?? 'ALL',
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ])->with('error', 'No posted activities found for payment approval.');
        }

        $totalHours = $activities->sum('hours');
        $totalAmount = $activities->sum(function ($activity) {
            return $activity->hours * (optional($activity->user->salary)->salary ?? 0);
        });

        $walletAddress = optional($activities->first()->user)->wallet_address;
        $walletNetwork = optional($activities->first()->user)->wallet_network ?: 'Solana';

        $referenceNumber = 'USDC-' . strtoupper(uniqid());

        PaymentPosting::create([
            'user_id' => $userId,
            'reference_number' => $referenceNumber,
            'wallet_address' => $walletAddress,
            'wallet_network' => $walletNetwork,
            'payment_method' => 'USDC (Solana)',
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'total_hours' => $totalHours,
            'total_amount' => $totalAmount,
            'status' => 'Approved',
            'payment_approved_by' => auth()->user()->name,
            'approved_at' => now(),
        ]);

        foreach ($activities as $activity) {
            $hourlyRate = optional($activity->user->salary)->salary ?? 0;
            $activity->payment_method = 'USDC (Solana)';
            $activity->payment_reference = $referenceNumber;
            $activity->payment_amount = $activity->hours * $hourlyRate;
            $activity->paid_at = now();
            $activity->payment_approved_by = auth()->user()->name;
            $activity->save();
        }

        return redirect()->route('Timekeeping.posted', [
            'members' => $request->members ?? 'ALL',
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ])->with('success', 'USDC payment approved for selected posted hours.');
    }

    /**
     * Prepare USDC transfer before approval
     * Shows modal with PHP to USDC conversion
     */
    public function prepareTransfer(Request $request, $userId)
    {
        try {
            $exchangeRateService = new ExchangeRateService();

            // Get activities for the user
            $query = TaskActivity::with('user.salary')
                ->where('user_id', $userId)
                ->whereNotNull('timekeeping_from')
                ->whereNotNull('timekeeping_to');

            if ($request->date_from) {
                $query->whereDate('timekeeping_to', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $query->whereDate('timekeeping_from', '<=', $request->date_to);
            }

            $activities = $query->get();

            if ($activities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No posted activities found',
                ], 404);
            }

            // Calculate totals
            $totalHours = $activities->sum('hours');
            $totalPhpAmount = $activities->sum(function ($activity) {
                return $activity->hours * (optional($activity->user->salary)->salary ?? 0);
            });

            // Look up existing Approved posting to get adjustments
            $existingPosting = PaymentPosting::with('adjustments')
                ->where('user_id', $userId)
                ->where('date_from', $request->date_from)
                ->where('date_to', $request->date_to)
                ->where('status', 'Approved')
                ->first();

            $adjustments = [];
            $adjAdds     = 0;
            $adjDeducts  = 0;

            if ($existingPosting) {
                foreach ($existingPosting->adjustments as $adj) {
                    $adjustments[] = [
                        'type'        => $adj->type,
                        'description' => $adj->description,
                        'amount'      => (float) $adj->amount,
                    ];
                    if ($adj->type === 'add') {
                        $adjAdds += $adj->amount;
                    } else {
                        $adjDeducts += $adj->amount;
                    }
                }
            }

            $netPhpAmount    = $totalPhpAmount + $adjAdds - $adjDeducts;
            $totalUsdcAmount = $exchangeRateService->convertPhpToUsdc($netPhpAmount);
            $exchangeRate    = $exchangeRateService->getFormattedRate();

            $user = $activities->first()->user;

            return response()->json([
                'success' => true,
                'data' => [
                    'user_id'          => $userId,
                    'user_name'        => $user->name,
                    'total_hours'      => $totalHours,
                    'gross_php_amount' => round($totalPhpAmount, 2),
                    'adj_adds'         => round($adjAdds, 2),
                    'adj_deducts'      => round($adjDeducts, 2),
                    'net_php_amount'   => round($netPhpAmount, 2),
                    'total_php_amount' => round($netPhpAmount, 2),
                    'total_usdc_amount'=> $totalUsdcAmount,
                    'exchange_rate'    => $exchangeRate,
                    'wallet_address'   => $user->wallet_address,
                    'adjustments'      => $adjustments,
                    'posting_id'       => $existingPosting ? $existingPosting->id : null,
                    'date_from'        => $request->date_from,
                    'date_to'          => $request->date_to,
                    'members'          => $request->members ?? 'ALL',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error preparing transfer', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error preparing transfer: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process USDC transfer via Phantom wallet
     */
    public function processUsdcTransfer(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer',
                'transaction_signature' => 'required|string',
                'usdc_amount' => 'required|numeric|min:0.01',
                'php_amount' => 'required|numeric|min:0.01',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
            ]);

            // Get user
            $user = User::findOrFail($validated['user_id']);

            // Verify transaction signature format (Solana signatures are base58 88 characters)
            // Base58 alphabet: 123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz (no 0, O, I, l)
            if (!preg_match('/^[1-9A-HJ-NP-Za-km-z]{88}$/', $validated['transaction_signature'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transaction signature format. Expected 88 character base58 string.',
                ], 422);
            }

            // Check if transaction signature already exists (prevent replay attacks)
            if (TokenTransfer::where('transaction_signature', $validated['transaction_signature'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This transaction has already been processed.',
                ], 422);
            }

            $solanaService = new SolanaTokenService();
            $transaction = $solanaService->getTransaction($validated['transaction_signature']);
            if (
                !$transaction ||
                isset($transaction['error']) ||
                empty($transaction['result']) ||
                !empty($transaction['result']['meta']['err'])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction could not be verified on Solana. Payment was not marked as paid.',
                ], 422);
            }

            // Get activities to mark as paid
            $query = TaskActivity::where('user_id', $validated['user_id'])
                ->whereNotNull('timekeeping_from')
                ->whereNotNull('timekeeping_to');

            if ($validated['date_from']) {
                $query->whereDate('timekeeping_to', '>=', $validated['date_from']);
            }
            if ($validated['date_to']) {
                $query->whereDate('timekeeping_from', '<=', $validated['date_to']);
            }

            $activities = $query->get();

            if ($activities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No activities found for this user and date range',
                ], 404);
            }

            // Check for existing posting
            $existingPayment = PaymentPosting::where('user_id', $validated['user_id'])
                ->where('date_from', $validated['date_from'])
                ->where('date_to', $validated['date_to'])
                ->first();

            if ($existingPayment && $existingPayment->status === 'Completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'A payment has already been completed for this date range.',
                ], 422);
            }

            // Some legacy Approved postings already have paid_at stamped; only block paid
            // activities when there is no open posting to complete.
            $alreadyPaid = $activities->where('paid_at', '!=', null)->count();
            if ($alreadyPaid > 0 && !$existingPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some activities in this date range have already been paid. Please select unpaid activities only.',
                ], 422);
            }

            $notes = 'USDC Transfer Amount: ' . $validated['usdc_amount'] . ' | Transaction: ' . substr($validated['transaction_signature'], 0, 20) . '...';

            if ($existingPayment) {
                // Promote existing Approved posting to Completed
                $existingPayment->update([
                    'status'               => 'Completed',
                    'total_amount'         => $validated['php_amount'],
                    'wallet_address'       => $user->wallet_address,
                    'payment_approved_by'  => auth()->user()->name,
                    'approved_at'          => now(),
                    'notes'                => $notes,
                ]);
                $payment         = $existingPayment;
                $referenceNumber = $payment->reference_number;
            } else {
                // Create a brand-new posting
                $referenceNumber = 'USDC-' . strtoupper(uniqid());
                $payment = PaymentPosting::create([
                    'user_id'              => $validated['user_id'],
                    'reference_number'     => $referenceNumber,
                    'wallet_address'       => $user->wallet_address,
                    'wallet_network'       => 'Solana',
                    'payment_method'       => 'USDC (Solana)',
                    'date_from'            => $validated['date_from'],
                    'date_to'              => $validated['date_to'],
                    'total_hours'          => $activities->sum('hours'),
                    'total_amount'         => $validated['php_amount'],
                    'status'               => 'Completed',
                    'payment_approved_by'  => auth()->user()->name,
                    'approved_at'          => now(),
                    'notes'                => $notes,
                ]);
            }

            // Record token transfer
            TokenTransfer::create([
                'user_id' => $validated['user_id'],
                'recipient_wallet' => $user->wallet_address,
                'amount' => $validated['usdc_amount'],
                'transaction_signature' => $validated['transaction_signature'],
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Update activities as paid
            foreach ($activities as $activity) {
                $hourlyRate = optional($activity->user->salary)->salary ?? 0;
                $activity->payment_method = 'USDC (Solana)';
                $activity->payment_reference = $referenceNumber;
                $activity->payment_amount = $activity->hours * $hourlyRate;
                $activity->paid_at = now();
                $activity->payment_approved_by = auth()->user()->name;
                $activity->save();
            }

            Log::info('USDC transfer processed', [
                'user_id' => $validated['user_id'],
                'amount_usdc' => $validated['usdc_amount'],
                'reference' => $referenceNumber,
                'transaction' => substr($validated['transaction_signature'], 0, 20),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'USDC payment transferred successfully',
                'data' => [
                    'reference_number' => $referenceNumber,
                    'transaction_signature' => $validated['transaction_signature'],
                    'amount_usdc' => $validated['usdc_amount'],
                    'amount_php' => $validated['php_amount'],
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error processing USDC transfer', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transfer processing failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {

        $dates[] = date( $format, $current );
        $current = strtotime( $step, $current );
    }

        return $dates;
    }

    private function normalizeActivityDates($activities)
    {
        return $activities->map(function ($activity) {
            if (!$activity->date) {
                $activity->timekeeping_date = null;
            } elseif (method_exists($activity->date, 'format')) {
                $activity->timekeeping_date = $activity->date->format('Y-m-d');
            } else {
                $activity->timekeeping_date = date('Y-m-d', strtotime($activity->date));
            }

            return $activity;
        });
    }
}
