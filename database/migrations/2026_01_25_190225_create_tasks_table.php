<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to events
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: If event deleted, all its tasks deleted too
            // Makes sense: tasks belong to specific events
            
            // ✅ REQUIRED: Assigned Planner (Event Planner/Admin only!)
            $table->foreignId('assigned_planner_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: Only planners can be assigned tasks
            // Why required (not nullable)? Every task must have someone responsible
            // If planner deleted, their tasks deleted too (cascade)
            // Note: Your requirements say "mandatory: only planners, no [opt]"
            
            // ✅ REQUIRED: Task Details
            $table->string('title');
            // Example: "Book wedding venue"
            // Why required? Must know what the task is!
            
            // ⚠️ NULLABLE: Task Description
            $table->text('description')->nullable();
            // Example: "Contact Grand Ballroom and confirm availability for June 15"
            // Why nullable? Title might be enough, description is extra detail
            
            // ✅ REQUIRED: Due Date
            $table->date('due_date');
            // Example: "2026-03-15" (3 months before event)
            // Why required? Tasks need deadlines for timeline planning
            
            // ✅ REQUIRED: Task Status with default
            $table->enum('status', ['pending', 'in_progress', 'done'])
                  ->default('pending');
            // Explanation:
            // - pending = not started yet
            // - in_progress = currently working on it
            // - done = completed
            // Why default 'pending'? New tasks start as pending
            
            // ⚠️ NULLABLE: Order Index
            $table->integer('order_index')->nullable();
            // Example: 1, 2, 3, 4... for sorting tasks in timeline
            // Why nullable? Optional - for manual task ordering/prioritization
            // Used for: Drag-and-drop reordering in UI
            
            // ✅ REQUIRED: Task Source
            $table->enum('source', ['default', 'custom'])
                  ->default('custom');
            // Explanation:
            // - default = auto-generated from event_types.default_tasks
            // - custom = manually created by planner
            // Why required with default? Track where task came from
            
            // ⚠️ NULLABLE: Completion Timestamp
            $table->timestamp('completed_at')->nullable();
            // Why nullable? Only filled when status changed to 'done'
            // Used for: Tracking when task was completed, performance metrics
            
            // ⚠️ NULLABLE: Task Dependency (Unary Relationship)
            $table->foreignId('depends_on_task_id')
                  ->nullable()
                  ->constrained('tasks')
                  ->onDelete('set null');
            // Explanation: Self-referencing foreign key - task depends on another task
            // Example: "Send invitations" depends on "Finalize guest list"
            // Why nullable? Not all tasks have dependencies
            // Why set null? If parent task deleted, child task still exists
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};