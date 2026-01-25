<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planner_profiles', function (Blueprint $table) {
            // Primary Key + Foreign Key to users
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
            
            // Planner-specific fields
            $table->text('bio')->nullable(); // Long text for biography
            $table->integer('years_experience')->default(0); // Years in business
            $table->json('specialties')->nullable(); // Array: ['weddings', 'corporate', ...]
            $table->string('portfolio_url')->nullable(); // Link to portfolio website
            $table->decimal('hourly_rate', 10, 2)->nullable(); // e.g., 150.00
            $table->text('availability_notes')->nullable(); // "Available weekends only"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planner_profiles');
    }
};
