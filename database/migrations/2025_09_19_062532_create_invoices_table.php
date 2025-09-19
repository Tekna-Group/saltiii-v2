<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('user_id');
    $table->string('invoice_number');
    $table->string('service');
    $table->text('description')->nullable();
    $table->decimal('amount', 10, 2);
    $table->string('paid_status')->default('pending'); // pending, paid, failed
    $table->string('payment_type')->nullable();        // card, bank transfer, etc.
    $table->string('payment_description')->nullable(); // Stripe PaymentIntent ID
    $table->timestamp('paid_on')->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
