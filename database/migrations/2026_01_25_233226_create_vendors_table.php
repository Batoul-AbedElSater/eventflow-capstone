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
        Schema::create('vendors', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Vendor Basic Info
            $table->string('name');
            // Example: "Royal Catering Services"
            // Why required? Must know vendor's business name
            
            // ✅ REQUIRED: Vendor Category
            $table->enum('category', [
                'Catering',
                'Venue',
                'Decoration',
                'Entertainment',
                'Photography',
                'Videography',
                'Floristry',
                'Music',
                'Transportation',
                'Other'
            ]);
            // Explanation: What service does this vendor provide?
            // Why required? Essential for filtering/searching vendors
            // Why enum? Limited, predefined service categories
            
            // ✅ REQUIRED: Contact Information
            $table->string('email');
            // Why required? Need to contact vendor
            // Note: NOT unique - different vendors might share company email
            
            $table->string('phone');
            // Why required? Primary contact method
            // Note: NOT unique - same reasoning as email
            
            // ⚠️ NULLABLE: Website URL
            $table->string('website')->nullable();
            // Example: "https://royalcatering.com"
            // Why nullable? Not all vendors have websites (especially small businesses)
            
            // ✅ DERIVED: Rating & Review Count
            $table->decimal('rating_avg', 3, 2)->default(0);
            // Example: 4.75 (out of 5.00)
            // Why default 0? New vendors have no ratings yet
            // Will be calculated from vendor_reviews table
            
            $table->integer('review_count')->default(0);
            // Example: 25 reviews
            // Why default 0? New vendors have no reviews
            // Will be updated when reviews added
            
            // ⚠️ NULLABLE: Description
            $table->text('description')->nullable();
            // Example: "Premium catering service specializing in traditional and modern cuisine"
            // Why nullable? Can be added later to improve vendor profile
            
            // ✅ REQUIRED: Location
            $table->string('location');
            // Example: "Baalbek, Lebanon"
            // Why required? Clients need to know vendor's service area
            // Important for: Distance, availability, logistics
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};