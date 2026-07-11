<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'assigned_planner_id')) {
                $table->foreignId('assigned_planner_id')
                      ->nullable()
                      ->after('user_id')
                      ->constrained('users')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'assigned_planner_id')) {
                $table->dropForeign(['assigned_planner_id']);
                $table->dropColumn('assigned_planner_id');
            }
        });
    }
};
