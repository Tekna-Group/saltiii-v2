@extends('layouts.header')

@section('content')
<div class="container mt-5">
    <h2>Subscribe to Continue</h2>
    <p class="text-muted mt-2">
        After a month, if you're not satisfied — <strong>30 days money-back guaranteed!</strong>
    </p>
    @include('error')
    <form method="POST" action="{{ url('subscribe-submit') }}" id="subscribe-form">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
        <input type="hidden" name="name" value="{{ auth()->user()->name }}">
        <input type="hidden" name="amount" id="amount" value="6.99">

        <div class="form-group mt-3">
            <label>Select Plan:</label>
            <select name="plan_id" id="plan_id" class="form-control">
                <option value="price_1SJFKKRthqNI30RMUcyMwRo3" data-amount="6.99">
                    Basic - $6.99/month
                </option>
            </select>
        </div>

        <div id="card-element" class="form-control mt-3"></div>
        <div id="card-errors" class="text-danger mt-2"></div>
        <input type="hidden" name="payment_method" id="payment_method">

        <button type='submit' class="btn btn-primary mt-3 w-100">Subscribe & Pay</button>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const planSelect = document.getElementById('plan_id');
    const amountInput = document.getElementById('amount');
    planSelect.addEventListener('change', e => {
        amountInput.value = e.target.selectedOptions[0].dataset.amount;
    });

    const form = document.getElementById('subscribe-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const { error, paymentMethod } = await stripe.createPaymentMethod({
            type: 'card',
            card: card
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
        } else {
            document.getElementById('payment_method').value = paymentMethod.id;
            form.submit();
        }
    });
</script>
@endsection
