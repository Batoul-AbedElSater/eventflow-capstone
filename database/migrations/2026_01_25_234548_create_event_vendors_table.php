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
        Schema::create('event_vendors', function (Blueprint $table) {
            // ✅ COMPOSITE PRIMARY KEY (both columns together)
            // This is a bridge/pivot table - no separate id column
            
            // ✅ REQUIRED: Foreign Key to events
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: If event deleted, all vendor associations deleted
            
            // ✅ REQUIRED: Foreign Key to vendors
            $table->foreignId('vendor_id')
                  ->constrained('vendors')
                  ->onDelete('cascade');
            // Explanation: If vendor deleted from system, associations deleted
            
            // ✅ COMPOSITE PRIMARY KEY
            $table->primary(['event_id', 'vendor_id']);
            // Explanation: 
            // - Same event can't have same vendor twice
            // - But same vendor can be in multiple events ✅
            // - And same event can have multiple vendors ✅
            
            // ✅ REQUIRED: Vendor Status for this event
            $table->enum('status', [
                'interested',   // Client considering this vendor
                'shortlisted',  // Vendor made it to shortlist
                'booked',       // Vendor officially hired
                'declined'      // Client decided not to use this vendor
            ])->default('interested');
            // Explanation: Track progression of vendor selection
            // Why default 'interested'? When first added, just browsing
            
            // ⚠️ NULLABLE: Agreed Amount
            $table->decimal('agreed_amount', 12, 2)->nullable();
            // Example: 15000.00 SAR
            // Why nullable? Only filled when vendor booked (negotiated price)
            // When status='interested' or 'shortlisted': NULL
            // When status='booked': filled with agreed price
            
            // ✅ REQUIRED: Payment Status
            $table->enum('payment_status', [
                'pending',      // Payment not made yet
                'paid',         // Fully paid
                'overdue'       // Payment deadline passed
            ])->default('pending');
            // Explanation: Track if vendor has been paid
            // Why default 'pending'? New bookings not paid yet
            
            // ⚠️ NULLABLE: Contract URL
            $table->string('contract_url')->nullable();
            // Example: "https://eventflow.com/storage/contracts/contract_123.pdf"
            // Why nullable? Not all vendors require formal contracts
            // Used for: Storing signed agreements, terms & conditions
            
            // ⚠️ NULLABLE: Notes
            $table->text('notes')->nullable();
            // Example: "Requires 50% deposit. Menu tasting scheduled for March 5."
            // Why nullable? Optional field for important details
            // Used for: Special requirements, reminders, conditions
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_vendors');
    }
};
