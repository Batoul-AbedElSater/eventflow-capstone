<?php

namespace App\Http\Controllers\Api\Planner;

use App\Http\Controllers\Controller;
use App\Models\AiBudgetDraft;
use App\Models\Event;
use App\Services\AIBudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudgetAiController extends Controller
{
    protected AIBudgetService $aiService;

    public function __construct(AIBudgetService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function show(Request $request, $eventId)
    {
        $event = Event::where('planner_id', $request->user()->id)
            ->findOrFail($eventId);

        $draft = AiBudgetDraft::where('event_id', $event->id)
            ->where('planner_id', $request->user()->id)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'event_id' => $event->id,
                'draft' => $draft,
            ],
        ]);
    }

    public function generate(Request $request, $eventId)
{
    // First: check if event exists and belongs to planner
    $event = Event::where('planner_id', $request->user()->id)->find($eventId);

    if (!$event) {
        return response()->json([
            'success' => false,
            'message' => 'Event not found.',
        ], 404);
    }

    // Check for cancelled status only
    if ($event->status === 'cancelled') {
        return response()->json([
            'success' => false,
            'message' => 'This event is cancelled. Budget cannot be generated for cancelled events.',
        ], 422);
    }

    try {
        // Load relationships
        $event->load([
            'eventType:id,name,description',
            'vendors:id,name,category,description',
            'budget.items:id,budget_id,category,title,estimated_cost,actual_cost,status,notes',
        ]);

        $existingBudget = $event->budget;
        $budgetItems = $existingBudget?->items ?? collect();

        $payload = [
            'event_id' => $event->id,
            'name' => $event->name,
            'description' => $event->description,
            'event_type' => $event->eventType?->name,
            'event_type_description' => $event->eventType?->description,
            'start_date' => $event->start_date?->format('Y-m-d'),
            'start_time' => $event->start_time,
            'end_date' => $event->end_date?->format('Y-m-d'),
            'end_time' => $event->end_time,
            'location' => $event->location_text,
            'guest_estimate' => $event->guest_estimate,
            'budget_overall' => (float) $event->budget_overall,
            'favorite_vendors' => $event->vendors->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'category' => $vendor->category,
                    'description' => $vendor->description,
                    'is_favorite' => (bool) ($vendor->pivot->is_favorite ?? false),
                ];
            })->values(),
            // Planner edits context from Budget Editor so AI generation can refine suggestions
            'planner_budget_context' => [
                'has_existing_budget' => (bool) $existingBudget,
                'status' => $existingBudget?->status,
                'total_client_budget' => $existingBudget?->total_client_budget ? (float) $existingBudget->total_client_budget : null,
                'planner_fee' => $existingBudget?->planner_fee ? (float) $existingBudget->planner_fee : null,
                'estimated_total' => $existingBudget?->estimated_total ? (float) $existingBudget->estimated_total : null,
                'actual_total' => $existingBudget?->actual_total ? (float) $existingBudget->actual_total : null,
                'planner_notes' => $existingBudget?->planner_notes,
                'items' => $budgetItems->map(function ($item) {
                    return [
                        'category' => $item->category,
                        'title' => $item->title,
                        'estimated_cost' => $item->estimated_cost !== null ? (float) $item->estimated_cost : null,
                        'actual_cost' => $item->actual_cost !== null ? (float) $item->actual_cost : null,
                        'status' => $item->status,
                        'notes' => $item->notes,
                    ];
                })->values(),
                'instructions' => 'Use this planner-edited budget context to refine and align new AI suggestions. Treat planner edits as higher-priority constraints.',
            ],
        ];

        $aiResponse = $this->aiService->generateSuggestions($payload);

        $draftStatus = $existingBudget?->status ?? 'draft';

        $draft = AiBudgetDraft::updateOrCreate(
            [
                'event_id' => $event->id,
                'planner_id' => $request->user()->id,
            ],
            [
                'ai_response' => $aiResponse,
                'status' => $draftStatus,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'AI budget suggestions generated successfully',
            'data' => [
                'event_id' => $event->id,
                'draft' => $draft,
                'ai_response' => $aiResponse,
            ],
        ]);

    } catch (\Exception $e) {
        Log::error('Budget AI generation failed', [
            'event_id' => $eventId,
            'error' => $e->getMessage(),
        ]);

        $message = app()->environment('local')
            ? 'Failed to generate budget suggestions: ' . $e->getMessage()
            : 'Failed to generate budget suggestions. Please try again.';

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}
}