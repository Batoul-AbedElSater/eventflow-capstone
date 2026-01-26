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
        Schema::create('expenses', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ✅ REQUIRED: Foreign Key to budget_categories
            $table->foreignId('budget_category_id')
                  ->constrained('budget_categories')
                  ->onDelete('cascade');
            // Explanation: Which budget category this expense belongs to
            // Example: "Food & Catering" category
            // If category deleted, its expenses deleted too
            // This links expense to specific event (through budget_category)
            
            // ✅ REQUIRED: Foreign Key to vendors
            $table->foreignId('vendor_id')
                  ->constrained('vendors')
                  ->onDelete('restrict');
            // Explanation: Which vendor received payment
            // Why restrict? Can't delete vendor if they have expense records
            // Protects financial history/audit trail
            
            // ✅ REQUIRED: Expense Description
            $table->string('description');
            // Example: "Catering deposit for 150 guests"
            // Why required? Must know what the expense is for
            // Important for: Accounting, tracking, reporting
            
            // ✅ REQUIRED: Expense Amount
            $table->decimal('amount', 12, 2);
            // Example: 5000.00 SAR
            // Why DECIMAL(12,2)? Same as budget_overall - precise money
            // Why required? Can't have expense without amount
            // Range: 0.01 to 9,999,999,999.99
            
            // ✅ REQUIRED: Payment Status
            $table->enum('status', [
                'pending',      // Not paid yet
                'paid',         // Payment completed
                'overdue'       // Payment deadline passed
            ])->default('pending');
            // Explanation: Track if expense has been paid
            // Why default 'pending'? New expenses not paid immediately
            // Used for: Cash flow tracking, payment reminders
            
            // ⚠️ NULLABLE: Payment Date
            $table->timestamp('paid_at')->nullable();
            // Why nullable? Only filled when status changes to 'paid'
            // Example: Expense created Feb 1, paid Feb 15
            // Used for: Financial reports, payment history
            
            // ⚠️ NULLABLE: Receipt/Invoice URL
            $table->string('receipt_url')->nullable();
            // Example: "https://eventflow.com/storage/receipts/receipt_456.pdf"
            // Why nullable? Not all expenses have digital receipts
            // Used for: Documentation, proof of payment, accounting
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
