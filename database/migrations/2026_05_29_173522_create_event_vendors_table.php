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
        if (!Schema::hasTable('event_vendors')) {
            Schema::create('event_vendors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
                $table->unique(['event_id','vendor_id']);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_vendors');
    }


};
