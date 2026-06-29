<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskFeedbackLoopsTable extends Migration
{
    public function up()
    {
        Schema::create('task_feedback_loops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->text('feedback');
            $table->string('status')->default('Open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_feedback_loops');
    }
}
