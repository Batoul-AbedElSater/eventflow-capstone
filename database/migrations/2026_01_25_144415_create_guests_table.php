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
            
            // ✅ REQUIRED: Guest Contact Information
            $table->string('name');
            $table->string('email');
            
            // ⚠️ NULLABLE: Phone (optional)
            $table->string('phone')->nullable();
            
            // ✅ REQUIRED: Unique invitation token (for RSVP link)
            $table->string('rsvp_token', 32)->nullable()->unique();
            // Note: Changed from 'invite_token' to 'rsvp_token' to match controller
            
            // ✅ REQUIRED: RSVP Status
            $table->enum('rsvp_status', ['pending', 'accepted', 'declined'])
                  ->default('pending');
            
            // ⚠️ NULLABLE: When guest responded
            $table->timestamp('rsvp_date')->nullable();
            // Note: Changed from 'rsvp_at' to 'rsvp_date' to match controller
            
            // ⚠️ NULLABLE: Guest message when RSVP
            $table->text('rsvp_message')->nullable();
            
            // ⚠️ NULLABLE: Dietary restrictions
            $table->string('dietary_restrictions')->nullable();
            
            // ✅ REQUIRED: Invitation tracking
            $table->boolean('invitation_sent')->default(false);
            $table->timestamp('invitation_sent_at')->nullable();
            
            // ✅ REQUIRED: Plus One Permission
            $table->boolean('plus_one_allowed')->default(false);
            
            // ⚠️ NULLABLE: Plus One Name
            $table->string('plus_one_name')->nullable();
            
            // ⚠️ NULLABLE: Notes about guest
            $table->text('notes')->nullable();
            
            // ⚠️ NULLABLE: Check-in timestamp (for event day)
            $table->timestamp('check_in_time')->nullable();
            
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