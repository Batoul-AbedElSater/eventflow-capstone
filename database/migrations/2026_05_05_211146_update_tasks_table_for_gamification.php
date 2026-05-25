<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add user_id (copy from assigned_planner_id)
            $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Add missing columns for gamification
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('description');
            $table->timestamp('due_date')->nullable()->after('deadline');
            $table->integer('progress')->default(0)->after('status');
        });
        
        // Copy data from assigned_planner_id to user_id
        DB::statement('UPDATE tasks SET user_id = assigned_planner_id WHERE user_id IS NULL');
        
        // Copy deadline to due_date
        DB::statement('UPDATE tasks SET due_date = deadline WHERE due_date IS NULL');
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'priority', 'due_date', 'progress']);
        });
    }
};