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
        Schema::create('notification_preferences', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to users (one preference record per user)
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: Which user these preferences belong to
            // Why unique? Each user has ONE preference record
            // If user deleted, their preferences deleted too
            
            // ✅ REQUIRED: Email Notifications Toggle
            $table->boolean('email_enabled')->default(true);
            // Explanation: Send notifications via email?
            // Why default true? Most users want email notifications
            // Example: "ahmad@test.com will receive notification emails"
            
            // ✅ REQUIRED: Push Notifications Toggle
            $table->boolean('push_enabled')->default(true);
            // Explanation: Send notifications via push (mobile/web)?
            // Why default true? Most users want push notifications
            // Example: Mobile app shows notification banner
            
            // ✅ REQUIRED: RSVP Alerts Toggle
            $table->boolean('rsvp_alerts')->default(true);
            // Explanation: Notify when guest RSVPs?
            // Why default true? Critical event information
            // Example: "Ahmad accepted your invitation"
            
            // ✅ REQUIRED: Task Alerts Toggle
            $table->boolean('task_alerts')->default(true);
            // Explanation: Notify about task deadlines?
            // Why default true? Important for planners
            // Example: "Task 'Book venue' due in 3 days"
            
            // ✅ REQUIRED: Message Alerts Toggle
            $table->boolean('message_alerts')->default(true);
            // Explanation: Notify about new messages?
            // Why default true? Important for communication
            // Example: "New message from Ali Khan"
            
            // ✅ REQUIRED: Budget Alerts Toggle
            $table->boolean('budget_alerts')->default(true);
            // Explanation: Notify when budget thresholds exceeded?
            // Why default true? Critical financial information
            // Example: "Food budget 95% used"
            
            $table->timestamps(); // created_at, updated_at
            // created_at = when preferences record created (user registration)
            // updated_at = when user changed their preferences
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};