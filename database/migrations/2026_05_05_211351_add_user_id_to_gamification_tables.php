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
            $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
        });
        
        // Copy planner_id to user_id
        DB::statement('UPDATE planner_gamification SET user_id = planner_id WHERE user_id IS NULL');
        
        // Add user_id to pomodoro_sessions
        Schema::table('pomodoro_sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
        });
        
        // Copy planner_id to user_id
        DB::statement('UPDATE pomodoro_sessions SET user_id = planner_id WHERE user_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('planner_gamification', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        
        Schema::table('pomodoro_sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};