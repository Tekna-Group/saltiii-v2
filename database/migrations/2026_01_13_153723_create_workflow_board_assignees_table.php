<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowBoardAssigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_board_assignees', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('user_id')->nullable(); // nullable for fallback only

            $table->boolean('replace_existing')->default(false);

            $table->string('fallback_rule')->default('keep'); // keep | mover | random | project_owner

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_board_assignees');
    }
}
