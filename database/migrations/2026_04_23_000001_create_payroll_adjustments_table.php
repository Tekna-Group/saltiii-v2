<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::create('payroll_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_posting_id');
            $table->enum('type', ['add', 'deduct']);
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('payment_posting_id')
                  ->references('id')
                  ->on('payment_postings')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_adjustments');
    }
}
