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
        Schema::create('attachments', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ⚠️ NULLABLE: Foreign Key to events (optional)
            $table->foreignId('event_id')
                  ->nullable()
                  ->constrained('events')
                  ->onDelete('cascade');
            // Explanation: Attachment for general event (e.g., venue photos)
            // Why nullable? Attachment might be for task or message instead
            // At least ONE of (event_id, task_id, message_id) must be filled
            
            // ⚠️ NULLABLE: Foreign Key to tasks (optional)
            $table->foreignId('task_id')
                  ->nullable()
                  ->constrained('tasks')
                  ->onDelete('cascade');
            // Explanation: Attachment for specific task (e.g., contract PDF)
            // Why nullable? Attachment might be for event or message instead
            // Example: Contract attached to "Book venue" task
            
            // ⚠️ NULLABLE: Foreign Key to messages (optional)
            $table->foreignId('message_id')
                  ->nullable()
                  ->constrained('messages')
                  ->onDelete('cascade');
            // Explanation: Attachment sent in message (e.g., photo shared in chat)
            // Why nullable? Attachment might be for event or task instead
            // Note: Messages table already has image_url for inline images
            // This is for additional file attachments (PDFs, docs, etc.)
            
            // ✅ REQUIRED: File URL
            $table->string('url');
            // Example: "storage/attachments/2026/01/contract_venue.pdf"
            // Why required? Every attachment must have a file location
            // Can be: Local storage path or cloud URL (S3, Cloudinary)
            
            // ✅ REQUIRED: File Type
            $table->enum('type', [
                'image',        // JPG, PNG, GIF
                'pdf',          // PDF documents
                'doc',          // Word documents (DOC, DOCX)
                'excel',        // Excel files (XLS, XLSX)
                'other'         // Other file types
            ]);
            // Explanation: What kind of file is this?
            // Why required? Used for icon display, preview capability
            // Application determines type from file extension
            
            $table->timestamps(); // created_at, updated_at
            // created_at = when file was uploaded
            // updated_at = when attachment record was modified
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};