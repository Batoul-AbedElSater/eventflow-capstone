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
        Schema::create('message_threads', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to events
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: Each event has its own conversation thread
            // If event deleted, conversation deleted too
            
            // ✅ REQUIRED: Foreign Key to client
            $table->foreignId('client_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: The client participating in this conversation
            // Must be a user with role='client'
            // If client deleted, thread deleted too
            
            // ✅ REQUIRED: Foreign Key to planner
            $table->foreignId('planner_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: The planner participating in this conversation
            // Must be a user with role='planner'
            // If planner deleted, thread deleted too
            
            $table->timestamps(); // created_at, updated_at
            
            // ✅ COMPOSITE UNIQUE CONSTRAINT
            // One unique thread per event-client-planner combination
            $table->unique(['event_id', 'client_id', 'planner_id']);
            // Explanation: 
            // - Same event can't have duplicate threads for same client-planner pair
            // - Each event gets ONE conversation thread between client and planner
            // - Prevents creating multiple threads for same conversation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_threads');
    }
};
