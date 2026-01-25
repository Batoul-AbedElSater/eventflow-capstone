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
        Schema::create('events', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Keys
            // Client who created this event (must exist)
            $table->foreignId('client_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: If client deleted, their events are deleted too
            
            // ⚠️ NULLABLE: Event planner assigned to this event
            // Why nullable? Event might be created but planner not assigned yet
            $table->foreignId('planner_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            // Explanation: If planner deleted, event remains but planner_id becomes null
            
            // ✅ REQUIRED: Type of event (Wedding, Birthday, etc.)
            $table->foreignId('event_type_id')
                  ->constrained('event_types')
                  ->onDelete('restrict');
            // Explanation: Can't delete event type if events are using it (restrict)
            
            // ✅ REQUIRED: Event Details
            $table->string('name');
            // Example: "Sarah & Ahmed's Wedding"
            // Why required? Every event needs a name!
            
            // ⚠️ NULLABLE: Event description
            $table->text('description')->nullable();
            // Example: "A beautiful garden wedding celebration"
            // Why nullable? Can be added later as planning progresses
            
            // ✅ REQUIRED: Event Dates
            $table->date('start_date');
            // Why required? Every event must have a start date
            
            // ⚠️ NULLABLE: End date (for multi-day events)
            $table->date('end_date')->nullable();
            // Why nullable? Single-day events don't need end_date
            // Example: Wedding = 1 day (no end_date needed)
            //          Conference = 3 days (needs end_date)
            
            // ✅ REQUIRED: Location
            $table->string('location_text');
            // Example: "Grand Ballroom, Marriott Hotel, Riyadh"
            // Why required? Clients need to know where the event is!
            
            // ✅ REQUIRED: Guest and Budget Info
            $table->integer('guest_estimate');
            // Example: 150 guests
            // Why required? Needed for planning capacity, catering, etc.
            
            $table->decimal('budget_overall', 12, 2);
            // Example: 50000.00 (50,000 SAR)
            // 12 total digits, 2 decimal places = max 9,999,999,999.99
            // Why required? Essential for budget tracking feature
            
            // ✅ REQUIRED: Event Status
            $table->enum('status', [
                'draft',        // Just created, planning not started
                'planned',      // Planning in progress
                'in_progress',  // Event is happening now
                'cancelled',    // Event was cancelled
                'completed'     // Event finished successfully
            ])->default('draft');
            // Why required with default? Every event must have a status
            // Default 'draft' = new events start as drafts
            
            // ⚠️ NULLABLE: Cancellation timestamp
            $table->timestamp('cancelled_at')->nullable();
            // Why nullable? Only filled if status = 'cancelled'
            
            // ⚠️ NULLABLE: Completion timestamp
            $table->timestamp('completed_at')->nullable();
            // Why nullable? Only filled when event is marked as completed
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
