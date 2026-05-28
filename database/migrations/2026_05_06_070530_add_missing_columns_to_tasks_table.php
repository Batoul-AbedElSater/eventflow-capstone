
/*
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add user_id if not exists
            if (!Schema::hasColumn('tasks', 'user_id')) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            }

            // Add priority if not exists
            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('description');
            }

            // Add progress if not exists
            if (!Schema::hasColumn('tasks', 'progress')) {
                $table->integer('progress')->default(0)->after('status');
            }

            // Add due_date if not exists
            if (!Schema::hasColumn('tasks', 'due_date')) {
                $table->timestamp('due_date')->nullable()->after('deadline');
            }
        });

        // Copy data from existing columns
        DB::statement('UPDATE tasks SET user_id = assigned_planner_id WHERE user_id IS NULL');
        DB::statement('UPDATE tasks SET due_date = deadline WHERE due_date IS NULL AND deadline IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('tasks', 'progress')) {
                $table->dropColumn('progress');
            }
            if (Schema::hasColumn('tasks', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};*/
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'user_id')) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('description');
            }

            if (!Schema::hasColumn('tasks', 'progress')) {
                $table->integer('progress')->default(0)->after('status');
            }

            if (!Schema::hasColumn('tasks', 'due_date')) {
                $table->timestamp('due_date')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('tasks', 'progress')) {
                $table->dropColumn('progress');
            }
            if (Schema::hasColumn('tasks', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};
