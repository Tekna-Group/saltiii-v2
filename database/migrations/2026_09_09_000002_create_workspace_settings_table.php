<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspaceSettingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('workspace_settings')) {
            Schema::create('workspace_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('company_name', 120);
                $table->string('timezone', 80)->default('Asia/Manila');
                $table->string('workweek_start', 20)->default('Monday');
                $table->decimal('standard_hours', 5, 2)->default(8);
                $table->string('currency', 10)->default('PHP');
                $table->string('date_format', 30)->default('M j, Y');
                $table->boolean('require_time_notes')->default(true);
                $table->boolean('weekly_summary')->default(true);
                $table->string('leave_approval_role', 30)->default('Project Lead');
                $table->unsignedInteger('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('workspace_settings');
    }
}
