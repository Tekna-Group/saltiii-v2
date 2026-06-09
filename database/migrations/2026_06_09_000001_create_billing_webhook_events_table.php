<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingWebhookEventsTable extends Migration
{
    public function up()
    {
        Schema::create('billing_webhook_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('subscription_id')->nullable()->index();
            $table->string('event_name')->index();
            $table->string('status')->default('pending')->index();
            $table->text('webhook_url')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('billing_webhook_events');
    }
}
