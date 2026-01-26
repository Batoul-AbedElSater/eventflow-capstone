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
        Schema::create('notifications', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to users (recipient)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: Who receives this notification
            // If user deleted, their notifications deleted too
            
            // ✅ REQUIRED: Foreign Key to events
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: Which event triggered this notification
            // If event deleted, related notifications deleted
            // Note: Most notifications are event-related
            
            // ✅ REQUIRED: Notification Type
            $table->enum('type', [
                'rsvp_update',      // Guest RSVP response received
                'task_due',         // Task deadline approaching/passed
                'new_message',      // New message in conversation
                'budget_alert',     // Budget threshold exceeded
                'event_created',    // New event created
                'vendor_booked',    // Vendor successfully booked
                'payment_due',      // Payment deadline approaching
                'event_reminder'    // Event date approaching
            ]);
            // Explanation: What kind of notification is this?
            // Why required? Determines icon, message, action
            // Used for: Filtering, grouping, styling
            
            // ✅ REQUIRED: Notification Data (JSON)
            $table->json('data_json');
            // Explanation: Additional context about notification
            // Stored as JSON for flexibility
            // Why required? Contains specific details
            
            // Examples by type:
            // rsvp_update: {"guest_name": "Ahmad", "status": "accepted"}
            // task_due: {"task_title": "Book venue", "due_date": "2026-03-15"}
            // new_message: {"sender_name": "Ali", "preview": "Can we meet..."}
            // budget_alert: {"category": "Food", "percentage": 95}
            
            // ✅ REQUIRED: Read Status
            $table->boolean('is_read')->default(false);
            // Explanation: Has user seen/read this notification?
            // Why default false? New notifications are unread
            // Used for: Badge count, highlighting new items
            
            $table->timestamps(); // created_at, updated_at
            // created_at = when notification was generated
            // updated_at = when notification was marked as read
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
