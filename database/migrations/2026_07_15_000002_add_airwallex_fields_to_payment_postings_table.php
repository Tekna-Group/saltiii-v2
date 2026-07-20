<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAirwallexFieldsToPaymentPostingsTable extends Migration
{
    public function up()
    {
        Schema::table('payment_postings', function (Blueprint $table) {
            $table->string('airwallex_transfer_id')->nullable()->unique();
            $table->string('airwallex_request_id', 50)->nullable()->unique();
            $table->string('airwallex_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payment_postings', function (Blueprint $table) {
            $table->dropUnique(['airwallex_transfer_id']);
            $table->dropUnique(['airwallex_request_id']);
            $table->dropColumn([
                'airwallex_transfer_id',
                'airwallex_request_id',
                'airwallex_status',
            ]);
        });
    }
}
