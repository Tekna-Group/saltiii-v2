<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentFieldsToTaskActivitiesTable extends Migration
{
    public function up()
    {
        Schema::table('task_activities', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('file');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->decimal('payment_amount', 12, 2)->nullable()->after('payment_reference');
            $table->timestamp('paid_at')->nullable()->after('payment_amount');
            $table->string('payment_approved_by')->nullable()->after('paid_at');
        });
    }

    public function down()
    {
        Schema::table('task_activities', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_reference',
                'payment_amount',
                'paid_at',
                'payment_approved_by',
            ]);
        });
    }
}
