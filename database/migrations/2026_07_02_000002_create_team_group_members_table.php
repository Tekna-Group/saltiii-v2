<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamGroupMembersTable extends Migration
{
    public function up()
    {
        Schema::create('team_group_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_group_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('role')->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['team_group_id', 'user_id']);
            $table->foreign('team_group_id')->references('id')->on('team_groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_group_members');
    }
}
