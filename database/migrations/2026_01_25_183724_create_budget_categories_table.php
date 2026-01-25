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
        Schema::create('budget_categories', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to events
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: If event deleted, its budget categories deleted too
            // Makes sense: budget categories belong to specific events
            
            // ✅ REQUIRED: Category Name
            $table->string('name');
            // Examples: "Venue", "Food & Catering", "Decoration", "Photography"
            // Why required? Must know what this budget is for!
            
            // ✅ REQUIRED: Allocated Amount
            $table->decimal('allocated_amount', 12, 2);
            // Example: Client allocates 15,000.00 SAR for venue
            // Why required? Need to know the budget limit for this category
            // Why DECIMAL(12,2)? Same as events.budget_overall - precise money handling
            
            // ✅ DERIVED: Spent Amount (calculated, not entered directly)
            $table->decimal('spent_amount', 12, 2)->default(0);
            // Explanation: Sum of all expenses in this category
            // Why default 0? When category created, nothing spent yet
            // Note: This will be updated by application code when expenses added
            
            // ⚠️ NULLABLE: Alert Threshold Percentage
            $table->integer('alert_threshold_pct')->nullable();
            // Example: 80 means "Alert me when 80% of budget used"
            // Why nullable? Optional feature - client may not want alerts
            // Why integer? Percentage values: 50, 75, 80, 90, 100
            // Used for: Budget warning notifications
            
            $table->timestamps(); // created_at, updated_at
            
            // ✅ COMPOSITE UNIQUE CONSTRAINT
            // Same event can't have duplicate category names
            $table->unique(['event_id', 'name']);
            // Example: Event 1 can have one "Venue" category
            // But Event 2 can also have "Venue" category ✅
            // Same event can't have two "Venue" categories ❌
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_categories');
    }
};