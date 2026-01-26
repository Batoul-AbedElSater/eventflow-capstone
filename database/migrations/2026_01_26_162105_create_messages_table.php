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
        Schema::create('messages', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to message_threads
            $table->foreignId('thread_id')
                  ->constrained('message_threads')
                  ->onDelete('cascade');
            // Explanation: Which conversation this message belongs to
            // If thread deleted, all messages in thread deleted too
            // Makes sense: messages are part of conversation
            
            // ✅ REQUIRED: Foreign Key to sender (user)
            $table->foreignId('sender_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: Who sent this message
            // Can be either client or planner (both are users)
            // If user deleted, their messages deleted too
            // Note: Check thread to verify sender is participant
            
            // ⚠️ NULLABLE: Message Body (Text)
            $table->text('body')->nullable();
            // Example: "Can we meet tomorrow to discuss the venue?"
            // Why nullable? Message might only contain image (no text)
            // Allows: text-only, image-only, or both
            
            // ⚠️ NULLABLE: Image URL
            $table->string('image_url')->nullable();
            // Example: "https://eventflow.com/storage/messages/venue_photo.jpg"
            // Why nullable? Most messages are text-only
            // Used for: Sharing photos, floor plans, designs
            
            // ✅ REQUIRED: Sent Timestamp
            $table->timestamp('sent_at')->useCurrent();
            // Explanation: Exact time message was sent
            // Why useCurrent()? Auto-fills with current timestamp
            // Used for: Message ordering, "sent 5 minutes ago"
            // Different from created_at: sent_at never changes
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};