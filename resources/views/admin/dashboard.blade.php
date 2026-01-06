@extends('layouts.header')

@section('css')
<style>
    .kpi-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,.05);
    }
    .kpi-title {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .kpi-value {
        font-size: 22px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="row g-3">

        <!-- Clients -->
        <div class="col-xl-4 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="kpi-title">Total Registered</div>
                    <div class="kpi-value">{{$users->count()}}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="kpi-title">Active Clients</div>
                    <div class="kpi-value text-success">{{$activeUsers->count()}}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="kpi-title">Inactive Clients</div>
                    <div class="kpi-value text-danger">{{$inactiveUsersCount}}</div>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        {{-- <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="kpi-title">Total Revenue (All Time)</div>
                    <div class="kpi-value">$ {{number_format($invoices->sum('amount'),2)}}</div>
                </div>
            </div>
        </div> --}}


    </div>
    <!-- Billing Summary Cards -->
<div class="row g-3 mb-4">

    <div class="col-xl-4 col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total Invoices</small>
                <h3 class="fw-bold mt-1">$ {{number_format($paidInvoices->sum('amount_paid')/100,2)}}</h3>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Paid</small>
                <h3 class="fw-bold text-success mt-1">$ {{number_format($paidInvoices->where('status','paid')->sum('amount_paid')/100,2)}}</h3>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <small class="text-muted">Unpaid</small>
                <h3 class="fw-bold text-warning mt-1">$ {{number_format($paidInvoices->where('status','open')->sum('amount_paid')/100,2)}}</h3>
            </div>
        </div>
    </div>

</div>

<!-- Invoice Table -->
<div class="card">
   

   <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Invoices</h5>
     
    </div>

    <div class="card-body">
        <div class="table-responsive">
             <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>From – To</th>
                        <th>Date Paid</th>
                        <th>Due Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paidInvoices as $invoice)
                        <tr>
                            <td><strong>{{ $invoice->number ?? $invoice->id }}</strong></td>
                            <td>{{ $invoice->customer_name ?? $invoice->customer }}</td>
                            <td>₱{{ number_format($invoice->amount_paid / 100, 2) }}</td>
                            <td>
                                @if($invoice->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($invoice->status == 'open')
                                    <span class="badge bg-warning">Unpaid</span>
                                @elseif($invoice->status == 'uncollectible')
                                    <span class="badge bg-danger">Uncollectible</span>
                                @elseif($invoice->status == 'void')
                                    <span class="badge bg-secondary">Voided</span>
                                @endif
                            </td>

                            <!-- From – To -->
                            <td>
                                @if($invoice->period_start && $invoice->period_end)
                                    {{ \Carbon\Carbon::createFromTimestamp($invoice->period_start)->format('M d, Y') }}
                                    –
                                    {{ \Carbon\Carbon::createFromTimestamp($invoice->period_end)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Date Paid -->
                            <td>
                                @if($invoice->status === 'paid' && $invoice->status_transitions->paid_at)
                                    {{ \Carbon\Carbon::createFromTimestamp($invoice->status_transitions->paid_at)->format('M d, Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Due Date -->
                            <td>
                                @if($invoice->due_date)
                                    {{ \Carbon\Carbon::createFromTimestamp($invoice->due_date)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                @if($invoice->hosted_invoice_url)
                                    <a href="{{ $invoice->hosted_invoice_url }}" target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        View Receipt
                                    </a>
                                @else
                                    <span class="text-muted">No Receipt</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No invoices found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

    <div class='row mt-4'>
        <div class='col-lg-12'>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Client Management</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Client Name</th>
                                       <th>Email</th>
                                    <th>Stripe Customer ID</th>
                                 
                                    <th>Plan</th>
                                    <th>Total Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($activeUsers as $user)
                                    <tr>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>

                                        <!-- Plan -->
                                        <td>
                                            @if($user->stripeCustomer)
                                                <span class="badge bg-primary">
                                                    {{ ucfirst($user->stripeCustomer->stripe_customer_id ?? 'N/A') }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->stripeCustomer)
                                                <span class="badge bg-primary">
                                                    Basic - $6.99/month
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">None</span>
                                            @endif
                                        </td>
                                        <td>
                                           $ {{number_format($user->total_paid, 2)}}
                                        </td>
                                        <!-- Status -->
                                        <td>
                                            @if(optional($user->stripeCustomer)->status === 'active')
                                                <span class="badge bg-success">🟢 Active</span>
                                            @else
                                                <span class="badge bg-danger">🔴 Inactive</span>
                                            @endif
                                        </td>

                                        
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>  

        </div>
    </div>

</div>

@endsection
