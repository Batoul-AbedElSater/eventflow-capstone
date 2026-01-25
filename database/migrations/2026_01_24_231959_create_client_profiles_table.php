<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_profiles', function (Blueprint $table) {
            // Primary Key (also Foreign Key to users)
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
            // foreignId() = creates BIGINT UNSIGNED
            // primary() = makes it primary key
            // constrained('users') = foreign key to users.id
            // onDelete('cascade') = if user deleted, delete this profile too
            
            // Optional client-specific fields
            $table->string('organization_name')->nullable(); // Company/org name
            $table->json('preferences')->nullable(); // Array of preferences (stored as JSON)
            $table->string('preferred_budget_range')->nullable(); // e.g., "$5000-$10000"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};