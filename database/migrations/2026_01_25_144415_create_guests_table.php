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
        Schema::create('guests', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to events
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: If event deleted, all its guests are deleted too
            // Makes sense: guests belong to specific events
            
            // ✅ REQUIRED: Guest Contact Information
            $table->string('name');
            // Why required? Must know who the guest is!
            
            $table->string('email');
            // Why required? Need email to send invitation
            // Note: NOT unique globally (same person can be guest at multiple events)
            
            $table->string('phone');
            // Why required? For SMS invitations and contact
            
            // ✅ REQUIRED: Unique invitation token
            $table->string('invite_token')->unique();
            // Explanation: Random unique string for RSVP link
            // Example: "abc123xyz789" 
            // Link: https://eventflow.com/rsvp/abc123xyz789
            // Why unique? Each guest needs their own personal link
            
            // ✅ REQUIRED: RSVP Status with default
            $table->enum('rsvp_status', ['pending', 'accepted', 'declined'])
                  ->default('pending');
            // Explanation: 
            // - pending = invitation sent, no response yet
            // - accepted = guest confirmed attendance
            // - declined = guest can't attend
            // Why default 'pending'? When first added, status is pending
            
            // ⚠️ NULLABLE: When guest responded
            $table->timestamp('rsvp_at')->nullable();
            // Why nullable? Only filled when guest actually responds
            // Example: Guest RSVPs on 2026-05-10, this stores that timestamp
            
            // ✅ REQUIRED: VIP Status
            $table->boolean('is_vip')->default(false);
            // Explanation: Mark important guests (family, VIPs)
            // Why default false? Most guests are not VIPs
            // Used for: priority seating, special treatment
            
            // ✅ REQUIRED: Plus One Permission
            $table->boolean('plus_one_allowed')->default(false);
            // Explanation: Can this guest bring someone?
            // Why default false? Not all guests can bring +1
            
            // ⚠️ NULLABLE: Plus One Name
            $table->string('plus_one_name')->nullable();
            // Why nullable? Only filled if guest brings someone
            // Example: Guest brings their partner "John Smith"
            
            // ⚠️ NULLABLE: Check-in timestamp
            $table->timestamp('check_in_time')->nullable();
            // Why nullable? Only filled when guest arrives at event
            // Used for: attendance tracking, event reports
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
