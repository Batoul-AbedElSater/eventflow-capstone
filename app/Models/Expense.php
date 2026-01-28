<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'budget_category_id',
        'vendor_id',
        'description',
        'amount',
        'status',
        'paid_at',
        'receipt_url',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    // ========================================
    // BOOT METHOD (Update budget category after expense changes)
    // ========================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Update category spent_amount after creating expense
        static::created(function (Expense $expense) {
            $expense->budgetCategory->updateSpentAmount();
        });

        // Update category spent_amount after updating expense
        static::updated(function (Expense $expense) {
            $expense->budgetCategory->updateSpentAmount();
        });

        // Update category spent_amount after deleting expense
        static::deleted(function (Expense $expense) {
            $expense->budgetCategory->updateSpentAmount();
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the budget category this expense belongs to.
     * Many-to-One: Expense -> BudgetCategory
     */
    public function budgetCategory()
    {
        return $this->belongsTo(BudgetCategory::class);
    }

    /**
     * Get the vendor who received payment.
     * Many-to-One: Expense -> Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if expense is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    /**
     * Mark expense as paid.
     */
    public function markAsPaid(?string $receiptUrl = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'receipt_url' => $receiptUrl ?? $this->receipt_url,
        ]);
    }

    /**
     * Mark expense as overdue.
     */
    public function markAsOverdue(): void
    {
        $this->update([
            'status' => 'overdue',
        ]);
    }

    /**
     * Check if expense has receipt.
     */
    public function hasReceipt(): bool
    {
        return !empty($this->receipt_url);
    }

    /**
     * Get payment status emoji.
     */
    public function getPaymentStatusEmoji(): string
    {
        return match($this->status) {
            'paid' => '✅',
            'pending' => '⏳',
            'overdue' => '🚨',
            default => '❓'
        };
    }

    /**
     * Get expense summary.
     */
    public function getSummary(): string
    {
        return "{$this->vendor->name}: {$this->description} - {$this->amount} SAR ({$this->status})";
    }

    /**
     * Get event through budget category.
     */
    public function getEvent()
    {
        return $this->budgetCategory->event;
    }
}
