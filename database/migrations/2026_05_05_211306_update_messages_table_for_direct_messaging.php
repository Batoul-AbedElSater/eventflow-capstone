<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add receiver_id (we'll populate it from thread_id later)
            $table->foreignId('receiver_id')->after('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Add message column (copy from body)
            $table->text('message')->nullable()->after('body');
            
            // Add read_at
            $table->timestamp('read_at')->nullable()->after('is_read');
        });
        
        // Copy body to message
        DB::statement('UPDATE messages SET message = body WHERE message IS NULL');
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['receiver_id', 'message', 'read_at']);
        });
    }
};