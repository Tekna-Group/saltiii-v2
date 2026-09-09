<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicShareFieldsToProjectsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('projects', 'public_share_token')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('public_share_token', 64)->nullable()->unique();
            });
        }

        if (!Schema::hasColumn('projects', 'public_share_enabled_at')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->timestamp('public_share_enabled_at')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('projects', 'public_share_enabled_at')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('public_share_enabled_at');
            });
        }

        if (Schema::hasColumn('projects', 'public_share_token')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('public_share_token');
            });
        }
    }
}
