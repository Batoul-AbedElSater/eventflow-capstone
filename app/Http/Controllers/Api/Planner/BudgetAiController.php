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

    // Check for completed/cancelled status
    if ($event->status === 'completed') {
        return response()->json([
            'success' => false,
            'message' => 'This event is completed. Budget cannot be generated for completed events.',
        ], 422);
    }

    if ($event->status === 'cancelled') {
        return response()->json([
            'success' => false,
            'message' => 'This event is cancelled. Budget cannot be generated for cancelled events.',
        ], 422);
    }

    if (!in_array($event->status, ['confirmed', 'in_progress'])) {
        return response()->json([
            'success' => false,
            'message' => 'Budget can only be generated for confirmed or in-progress events.',
        ], 422);
    }

    try {
        // Load relationships
        $event->load(['eventType:id,name,description', 'vendors:id,name,category,description']);

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
        ];

        $aiResponse = $this->aiService->generateSuggestions($payload);

        $draft = AiBudgetDraft::updateOrCreate(
            [
                'event_id' => $event->id,
                'planner_id' => $request->user()->id,
            ],
            [
                'ai_response' => $aiResponse,
                'status' => 'draft',
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