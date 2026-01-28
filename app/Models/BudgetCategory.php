<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'name',
        'allocated_amount',
        'spent_amount',
        'alert_threshold_pct',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'allocated_amount' => 'decimal:2',
            'spent_amount' => 'decimal:2',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the event this budget category belongs to.
     * Many-to-One: BudgetCategory -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get expenses in this budget category.
     * One-to-Many: BudgetCategory -> Expenses
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Update spent_amount by summing all expenses.
     */
    public function updateSpentAmount(): void
    {
        $this->update([
            'spent_amount' => $this->expenses()->sum('amount'),
        ]);
    }

    /**
     * Get remaining budget.
     */
    public function getRemainingAmount(): float
    {
        return $this->allocated_amount - $this->spent_amount;
    }

    /**
     * Get percentage of budget used.
     */
    public function getPercentageUsed(): float
    {
        if ($this->allocated_amount == 0) {
            return 0;
        }
        
        return ($this->spent_amount / $this->allocated_amount) * 100;
    }

    /**
     * Check if budget threshold exceeded.
     */
    public function isThresholdExceeded(): bool
    {
        if (is_null($this->alert_threshold_pct)) {
            return false;
        }
        
        return $this->getPercentageUsed() >= $this->alert_threshold_pct;
    }

    /**
     * Check if over budget.
     */
    public function isOverBudget(): bool
    {
        return $this->spent_amount > $this->allocated_amount;
    }

    /**
     * Get budget status.
     */
    public function getBudgetStatus(): string
    {
        $percentage = $this->getPercentageUsed();
        
        if ($percentage >= 100) {
            return 'over_budget';
        } elseif ($percentage >= 80) {
            return 'warning';
        } else {
            return 'good';
        }
    }

    /**
     * Get budget status emoji.
     */
    public function getBudgetStatusEmoji(): string
    {
        return match($this->getBudgetStatus()) {
            'over_budget' => '🔴',
            'warning' => '🟡',
            'good' => '🟢',
            default => '⚪'
        };
    }

    /**
     * Add expense and update spent amount.
     */
    public function addExpense(array $expenseData): Expense
    {
        $expense = $this->expenses()->create($expenseData);
        $this->updateSpentAmount();
        
        // Check if threshold exceeded and send notification
        if ($this->isThresholdExceeded()) {
            $this->sendBudgetAlert();
        }
        
        return $expense;
    }

    /**
     * Send budget alert notification.
     */
    protected function sendBudgetAlert(): void
    {
        // Create notification for client
        Notification::create([
            'user_id' => $this->event->client_id,
            'event_id' => $this->event_id,
            'type' => 'budget_alert',
            'data_json' => [
                'category_id' => $this->id,
                'category_name' => $this->name,
                'allocated' => $this->allocated_amount,
                'spent' => $this->spent_amount,
                'percentage' => $this->getPercentageUsed(),
                'threshold' => $this->alert_threshold_pct,
            ],
            'is_read' => false,
        ]);
    }
}
