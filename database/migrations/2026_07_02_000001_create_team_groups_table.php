<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('team_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('owner_id')->index();
            $table->unsignedInteger('billing_user_id')->nullable()->index();
            $table->string('name');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('billing_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_groups');
    }
}
