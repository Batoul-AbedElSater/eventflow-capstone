<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add user_id to planner_gamification
        Schema::table('planner_gamification', function (Blueprint $table) {
            if (!Schema::hasColumn('planner_gamification', 'user_id')) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            }
        });

        // Add user_id to pomodoro_sessions
        Schema::table('pomodoro_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('pomodoro_sessions', 'user_id')) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('planner_gamification', function (Blueprint $table) {
            if (Schema::hasColumn('planner_gamification', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });

        Schema::table('pomodoro_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('pomodoro_sessions', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
