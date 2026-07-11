<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BudgetService
{
    public function importFromAiDraft(Event $event, int $plannerId): Budget
    {
        $draft = $event->aiBudgetDraft;

        if (! $draft) {
            throw new RuntimeException('Generate AI budget first before importing.');
        }

        $aiData = $draft->ai_response ?? [];
        $categories = collect($aiData['categories'] ?? []);

        return DB::transaction(function () use ($event, $plannerId, $aiData, $categories) {
            $budget = Budget::firstOrCreate(
                ['event_id' => $event->id],
                [
                    'planner_id' => $plannerId,
                    'status' => Budget::STATUS_DRAFT,
                    'shared_with_client' => false,
                ]
            );

            if ($budget->items()->exists()) {
                throw new RuntimeException('Budget already imported. Edit existing items instead.');
            }

            $budget->fill([
                'planner_id' => $plannerId,
                'total_client_budget' => $aiData['total_client_budget'] ?? $event->budget_overall ?? null,
                'planner_fee' => $aiData['planner_fee_amount'] ?? null,
                'total_assistant_fees' => $aiData['total_assistant_fees'] ?? null,
                'estimated_total' => $aiData['final_budget_for_categories'] ?? null,
                'planner_notes' => $this->buildPlannerNotesFromAi($aiData),
                'status' => Budget::STATUS_DRAFT,
                'shared_with_client' => false,
            ])->save();

            foreach ($categories as $category) {
                $budget->items()->create($this->mapAiCategoryToBudgetItemPayload($category));
            }

            return $this->recalculateTotals($budget->fresh('items'));
        });
    }

    public function recalculateTotals(Budget $budget): Budget
    {
        $estimatedTotal = (float) $budget->items()->sum('estimated_cost');
        $assistantFeesTotal = (float) $budget->items()->sum('assistant_fee');
        $hasActualCosts = $budget->items()->whereNotNull('actual_cost')->exists();
        $actualTotal = $hasActualCosts ? (float) $budget->items()->whereNotNull('actual_cost')->sum('actual_cost') : null;

        $budget->update([
            'estimated_total' => $estimatedTotal,
            'total_assistant_fees' => $assistantFeesTotal > 0 ? $assistantFeesTotal : $budget->total_assistant_fees,
            'actual_total' => $actualTotal,
        ]);

        return $budget->fresh('items');
    }

    private function buildPlannerNotesFromAi(array $aiData): ?string
    {
        $warnings = collect($aiData['warnings'] ?? [])->filter()->values();
        $questions = collect($aiData['planner_questions'] ?? [])->filter()->values();
        $suggestedAssistantWork = collect($aiData['suggested_assistant_work'] ?? [])->filter()->values();

        $segments = [];

        if ($warnings->isNotEmpty()) {
            $segments[] = 'Warnings: '.$warnings->implode(' | ');
        }

        if ($questions->isNotEmpty()) {
            $segments[] = 'Questions: '.$questions->implode(' | ');
        }

        if ($suggestedAssistantWork->isNotEmpty()) {
            $segments[] = 'Suggested assistant work: '.$suggestedAssistantWork->implode(' | ');
        }

        return empty($segments) ? null : implode(PHP_EOL, $segments);
    }

    private function mapAiCategoryToBudgetItemPayload($category): array
    {
        $categoryName = trim((string) ($category['category'] ?? $category['name'] ?? 'General'));
        $assistantWork = collect($category['suggested_assistant_work'] ?? [])
            ->filter()
            ->values()
            ->implode('; ');

        $notes = collect([
            $category['guest_based_note'] ?? null,
            $assistantWork ? "Suggested assistant work: {$assistantWork}" : null,
        ])->filter()->implode(PHP_EOL);

        return [
            'category' => $categoryName !== '' ? $categoryName : 'General',
            'title' => $category['title'] ?? $categoryName,
            'description' => $category['description'] ?? null,
            'estimated_cost' => (float) ($category['estimated_cost'] ?? 0),
            'suggested_orders' => $category['suggested_orders'] ?? [],
            'status' => BudgetItem::STATUS_PENDING,
            'notes' => $notes ?: null,
        ];
    }

}
