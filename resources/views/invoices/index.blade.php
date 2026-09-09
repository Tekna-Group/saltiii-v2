@extends('layouts.header')

@section('title', 'Invoices | SALTiii')

@section('content')
@if(session('success'))<div class="alert alert-success d-flex align-items-center gap-2"><i class="ri-checkbox-circle-line" aria-hidden="true"></i>{{ session('success') }}</div>@endif

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3"><div><h5 class="card-title mb-1">Billing history</h5><p class="text-muted mb-0 fs-12">Review subscription invoices and payment status.</p></div><a class="btn btn-sm btn-soft-primary" href="{{ url('/subscription-plan') }}"><i class="ri-bank-card-line me-1" aria-hidden="true"></i>Manage plan</a></div>
    <div class="card-body">
        @if($invoices->isEmpty())
            <div class="empty-state text-center"><div class="avatar-lg mx-auto mb-3"><span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-2"><i class="ri-file-list-3-line"></i></span></div><h5>No invoices yet</h5><p class="text-muted mb-0">Your billing records will appear here after your first payment.</p></div>
        @else
            <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Invoice</th><th>Service</th><th>Description</th><th>Amount</th><th>Status</th><th>Payment</th><th>Paid on</th><th class="text-end">Action</th></tr></thead><tbody>
                @foreach($invoices as $invoice)
                    <tr><td><strong>{{ $invoice->invoice_number }}</strong></td><td>{{ $invoice->service }}</td><td class="text-muted">{{ $invoice->description }}</td><td class="fw-semibold">${{ number_format($invoice->amount, 2) }}</td><td>@switch($invoice->paid_status) @case('Paid') <span class="badge bg-success-subtle text-success"><i class="ri-check-line"></i> Paid</span> @break @case('failed') <span class="badge bg-danger-subtle text-danger"><i class="ri-close-line"></i> Failed</span> @break @default <span class="badge bg-warning-subtle text-warning"><i class="ri-time-line"></i> Pending</span> @endswitch</td><td>{{ $invoice->payment_type ?? '—' }}</td><td>{{ $invoice->paid_on ? date('M d, Y',strtotime($invoice->paid_on)) : '—' }}</td><td class="text-end">@if($invoice->paid_status === 'Pending')<a href="{{ route('invoices.pay', $invoice->id) }}" class="btn btn-sm btn-primary">Pay now</a>@else<span class="text-muted fs-12">Complete</span>@endif</td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </div>
</div>
@endsection
