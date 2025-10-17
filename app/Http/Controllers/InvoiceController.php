<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use Stripe\Stripe;
use Stripe\Charge;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    //
     // List all invoices
    public function index()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->get();
        return view('invoices.index', compact('invoices'));
    }

    // Create a new invoice automatically
    public function createInvoice()
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'service' => 'Web Hosting Plan',
            'description' => 'Monthly web hosting service',
            'amount' => 50000.00,
            'paid_status' => 'Pending',
            'user_id' => auth()->user()->id
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice Created Successfully!');
    }

    // Show payment page
    public function pay($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('invoices.pay', compact('invoice'));
    }

    // Process payment with Stripe
    public function processPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $charge = Charge::create([
                'amount' => $invoice->amount * 100, // Stripe amount in cents
                'currency' => 'usd',
                'description' => $invoice->description,
                'source' => $request->stripeToken,
            ]);

            $invoice->update([
                'paid_status' => 'Paid',
                'payment_type' => 'Stripe',
                'payment_description' => $charge->id,
                'paid_on' => Carbon::now()
            ]);

            return redirect()->route('invoices.index')->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
