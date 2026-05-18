<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentPostingsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_postings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->string('reference_number')->unique();
            $table->string('wallet_address')->nullable();
            $table->string('wallet_network')->nullable();
            $table->string('payment_method')->default('USDC (Solana)');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->decimal('total_hours', 12, 2)->default(0);
            $table->decimal('total_amount', 16, 2)->default(0);
            $table->string('status')->default('Approved');
            $table->string('payment_approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_postings');
    }
}
