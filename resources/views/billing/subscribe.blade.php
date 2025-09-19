<script src="https://js.stripe.com/v3/"></script>

<form id="subscription-form">
    <div id="card-element" style="padding: 10px; border:1px solid #ccc;"></div>
    <button type="submit" id="submit-button">Subscribe</button>
</form>

<script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    document.getElementById('subscription-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            alert(error.message);
            return;
        }

        fetch("{{ route('billing.subscribe') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_method_id: paymentMethod.id
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Subscription created successfully!');
            } else {
                alert('Subscription failed: ' + data.message);
            }
        });
    });
</script>
