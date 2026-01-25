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
        Schema::create('event_types', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Event type name (must be unique)
            // Examples: "Wedding", "Birthday", "Corporate Event"
            $table->string('name')->unique();
            
            // ✅ NULLABLE: Default tasks for this event type
            // Stored as JSON array: ["Book venue", "Send invitations", "Arrange catering"]
            // Why nullable? New event types might not have default tasks defined yet
            $table->json('default_tasks')->nullable();
            
            // ✅ NULLABLE: Description of the event type
            // Example: "Perfect for celebrating milestone birthdays with family and friends"
            // Why nullable? Admin can add this later
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};
