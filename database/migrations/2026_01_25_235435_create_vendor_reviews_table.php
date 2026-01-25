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
        Schema::create('vendor_reviews', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to vendors
            $table->foreignId('vendor_id')
                  ->constrained('vendors')
                  ->onDelete('cascade');
            // Explanation: Which vendor is being reviewed
            // If vendor deleted, all their reviews deleted too
            
            // ✅ REQUIRED: Foreign Key to events
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: Which event was the vendor hired for
            // Important: Links review to specific experience
            // If event deleted, reviews for that event deleted
            
            // ✅ REQUIRED: Foreign Key to client (reviewer)
            $table->foreignId('client_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // Explanation: Which client wrote this review
            // Must be a user with role='client'
            // If client deleted, their reviews deleted
            // Note: Should match event.client_id (same client who hosted event)
            
            // ✅ REQUIRED: Rating Score
            $table->decimal('rating', 3, 2);
            // Example: 4.50 (out of 5.00)
            // Why DECIMAL(3,2)? 
            // - 3 total digits, 2 decimals
            // - Range: 0.00 to 9.99
            // - But application validates 0.00 to 5.00
            // Why required? Can't have review without rating
            
            // ⚠️ NULLABLE: Review Comment
            $table->text('comment')->nullable();
            // Example: "Excellent service! Food was delicious and staff very professional."
            // Why nullable? Client might rate without writing comment
            // Some clients just give stars, no text
            
            $table->timestamps(); // created_at, updated_at
            
            // ✅ COMPOSITE UNIQUE CONSTRAINT
            // One review per vendor per event per client
            $table->unique(['vendor_id', 'event_id', 'client_id']);
            // Explanation:
            // - Client can't review same vendor twice for same event
            // - But same client can review same vendor for DIFFERENT events ✅
            // - And different clients can review same vendor for same event ✅
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_reviews');
    }
};