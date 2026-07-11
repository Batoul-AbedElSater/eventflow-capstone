<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Event;
use App\Models\AiBudgetDraft;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class BudgetController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizePlannerEvent($event);

        $event->load([
            'eventType',
            'aiBudgetDraft',
            'budget.items',
        ]);

        return view('planner.events.budget.index', compact('event'));
    }

    public function draft(Event $event)
    {
        $this->authorizePlannerEvent($event);

        $event->load([
            'eventType',
            'aiBudgetDraft',
            'budget.items',
        ]);

        return view('planner.events.budget.draft', compact('event'));
    }

    public function editor(Event $event)
    {
        $this->authorizePlannerEvent($event);

        $event->load([
            'eventType',
            'aiBudgetDraft',
            'budget.items',
        ]);

        if (! $event->budget) {
            return redirect()
                ->route('planner.events.budget.draft', $event)
                ->with('error', 'Import AI suggestions first to open the Budget Editor.');
        }

        return view('planner.events.budget.editor', compact('event'));
    }

    public function import(Request $request, Event $event, BudgetService $budgetService)
    {
        $this->authorizePlannerEvent($event);
        $event->load('aiBudgetDraft', 'budget.items');

        try {
            $budgetService->importFromAiDraft($event, Auth::id());
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('planner.events.budget.draft', $event)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('planner.events.budget.editor', $event)
            ->with('success', 'AI suggestions imported. Welcome to your Budget Editor.');
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizePlannerEvent($event);

        $budget = $event->budget;

        if (! $budget) {
            return redirect()
                ->route('planner.events.budget.draft', $event)
                ->with('error', 'Import AI suggestions first to initialize the editable budget.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', Budget::STATUSES)],
            'shared_with_client' => ['nullable', 'boolean'],
            'total_client_budget' => ['nullable', 'numeric', 'min:0'],
            'planner_fee' => ['nullable', 'numeric', 'min:0'],
            'planner_notes' => ['nullable', 'string'],
        ]);

        // Keep planner selection as the source of truth for status.
        $status = $validated['status'];
        $sharedWithClient = $status === Budget::STATUS_SHARED
            ? true
            : (bool) ($validated['shared_with_client'] ?? false);

        $budget->update([
            'status' => $status,
            'shared_with_client' => $sharedWithClient,
            'total_client_budget' => $validated['total_client_budget'] ?? $budget->total_client_budget,
            'planner_fee' => $validated['planner_fee'] ?? $budget->planner_fee,
            'planner_notes' => $validated['planner_notes'] ?? null,
        ]);

        AiBudgetDraft::where('event_id', $event->id)
            ->where('planner_id', Auth::id())
            ->update(['status' => $status]);

        return redirect()
            ->route('planner.events.budget.editor', $event)
            ->with('success', 'Budget details updated.');
    }

    public function storeItem(Request $request, Event $event, BudgetService $budgetService)
    {
        $this->authorizePlannerEvent($event);

        $budget = $event->budget;

        if (! $budget) {
            return redirect()
                ->route('planner.events.budget.draft', $event)
                ->with('error', 'Import AI suggestions first to initialize the editable budget.');
        }

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'estimated_cost' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', BudgetItem::STATUSES)],
            'notes' => ['nullable', 'string'],
        ]);

        $budget->items()->create($validated);
        $budgetService->recalculateTotals($budget->fresh('items'));

        return redirect()
            ->route('planner.events.budget.editor', $event)
            ->with('success', 'Budget item added.');
    }

    public function updateItem(Request $request, Event $event, BudgetItem $item, BudgetService $budgetService)
    {
        $this->authorizePlannerEvent($event);
        $this->authorizeBudgetItem($event, $item);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'estimated_cost' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', BudgetItem::STATUSES)],
            'notes' => ['nullable', 'string'],
        ]);

        $item->update($validated);
        $budgetService->recalculateTotals($item->budget->fresh('items'));

        return redirect()
            ->route('planner.events.budget.editor', $event)
            ->with('success', 'Budget item updated.');
    }

    public function destroyItem(Event $event, BudgetItem $item, BudgetService $budgetService)
    {
        $this->authorizePlannerEvent($event);
        $this->authorizeBudgetItem($event, $item);

        $budget = $item->budget;
        $item->delete();
        $budgetService->recalculateTotals($budget->fresh('items'));

        return redirect()
            ->route('planner.events.budget.editor', $event)
            ->with('success', 'Budget item removed.');
    }

    private function authorizePlannerEvent(Event $event): void
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }
    }

    private function authorizeBudgetItem(Event $event, BudgetItem $item): void
    {
        if ($item->budget->event_id !== $event->id || $item->budget->planner_id !== Auth::id()) {
            abort(403);
        }
    }
}
