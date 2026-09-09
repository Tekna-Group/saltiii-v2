<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveRequestsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('leave_requests')) {
            Schema::create('leave_requests', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->index();
                $table->string('leave_type', 40);
                $table->date('start_date');
                $table->date('end_date');
                $table->text('reason');
                $table->string('status', 20)->default('Pending')->index();
                $table->unsignedInteger('reviewed_by')->nullable()->index();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_note')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'start_date', 'end_date']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('leave_requests');
    }
}
