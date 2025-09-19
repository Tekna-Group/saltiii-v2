@extends('layouts.header')

@section('content')
<h2>Invoices</h2>
<a href="{{ route('invoices.create') }}" class="btn btn-primary mb-3">Generate New Invoice</a>

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
            <td>₱ {{ number_format($invoice->amount, 2) }}</td>
            <td>
                @if($invoice->paid_status === 'Paid')
                    <span class="badge badge-success">Paid</span>
                @else
                    <span class="badge badge-warning">Pending</span>
                @endif
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
