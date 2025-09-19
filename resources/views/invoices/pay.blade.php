@extends('layouts.app')

@section('content')
<h2>Pay Invoice</h2>

<div class="card">
    <div class="card-body">
        <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Service:</strong> {{ $invoice->service }}</p>
        <p><strong>Description:</strong> {{ $invoice->description }}</p>
        <p><strong>Amount:</strong> ₱ {{ number_format($invoice->amount, 2) }}</p>

        <form action="{{ route('invoices.processPayment', $invoice->id) }}" method="POST" id="payment-form">
            @csrf
            <div class="form-group">
                <label for="card-element">Credit or Debit Card</label>
                <div id="card-element" class="form-control"></div>
                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
            </div>

            <button class="btn btn-success mt-3">Pay ₱ {{ number_format($invoice->amount, 2) }}</button>
        </form>
    </div>
</div>
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env("STRIPE_KEY") }}');
    var elements = stripe.elements();
    var card = elements.create('card');
    card.mount('#card-element');

    card.on('change', function(event) {
        var displayError = document.getElementById('card-errors');
        displayError.textContent = event.error ? event.error.message : '';
    });

    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                document.getElementById('card-errors').textContent = result.error.message;
            } else {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', result.token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    });
</script>
@endsection
