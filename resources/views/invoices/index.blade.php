@extends('layouts.header')

@section('content')
<h2>Invoices</h2>
{{-- <a href="{{ route('invoices.create') }}" class="btn btn-primary mb-3">Generate New Invoice</a> --}}

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Invoice #</th>
            <th>Service</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Type</th>
            <th>Paid On</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->service }}</td>
            <td>{{ $invoice->description }}</td>
            <td>$ {{ number_format($invoice->amount, 2) }}</td>
            <td>
                @switch($invoice->paid_status)
                    @case('Paid')
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> Paid
                        </span>
                        @break

                    @case('failed')
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle"></i> Failed
                        </span>
                        @break

                    @default
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock"></i> Pending
                        </span>
                @endswitch
            </td>
            <td>{{ $invoice->payment_type ?? '-' }}</td>
            <td>{{ $invoice->paid_on ? date('Y-m-d H:i',strtotime($invoice->paid_on)) : '-' }}</td>
            <td>
                @if($invoice->paid_status === 'Pending')
                    <a href="{{ route('invoices.pay', $invoice->id) }}" class="btn btn-sm btn-primary">Pay Now</a>
                @else
                    <button class="btn btn-sm btn-secondary" disabled>Paid</button>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
