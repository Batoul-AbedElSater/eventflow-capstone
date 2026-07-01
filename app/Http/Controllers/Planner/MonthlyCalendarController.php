<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MonthlyCalendarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
            'date' => 'nullable|date',
        ]);

        $user = Auth::user();

        if ($request->filled('month')) {
            $requestedDate = Carbon::parse($request->month . '-01');
        } elseif ($request->filled('date')) {
            $requestedDate = Carbon::parse($request->date);
        } else {
            $requestedDate = Carbon::now();
        }

        $monthStart = $requestedDate->copy()->startOfMonth();
        $monthEnd = $requestedDate->copy()->endOfMonth();

        $calendarStart = $monthStart->copy()->startOfWeek();
        $calendarEnd = $monthEnd->copy()->endOfWeek();

        $eventsByDay = Event::where('planner_id', $user->id)
            ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
            ->whereBetween('start_date', [
                $calendarStart->copy()->startOfDay(),
                $calendarEnd->copy()->endOfDay(),
            ])
            ->with(['eventType', 'client'])
            ->orderBy('start_date', 'asc')
            ->get()
            ->groupBy(fn ($event) => $event->start_date->format('Y-m-d'));

        $calendarDays = [];
        $day = $calendarStart->copy();

        while ($day->lte($calendarEnd)) {
            $dateKey = $day->format('Y-m-d');
            $dayEvents = $eventsByDay->get($dateKey, collect())->values();

            $dots = $dayEvents->map(function ($event) {
                return [
                    'event_id' => $event->id,
                    'status' => $event->status,
                    'color' => $this->eventStatusColor($event->status),
                ];
            })->values();



            $visibleEvents = $dayEvents->take(3)->map(function ($event) {
            return [
        'id' => $event->id,
        'name' => Str::limit($event->name, 18),
        'full_name' => $event->name,
        'status' => $event->status,
        'color' => $this->eventStatusColor($event->status),
        'url' => route('planner.events.show', $event->id),
    ];
          })->values();

            $calendarDays[] = [
                'date' => $dateKey,
                'day_name' => $day->format('l'),
                'day_number' => $day->format('d'),
                'month' => $day->format('M'),
                'is_today' => $day->isToday(),
                'is_current_month' => $day->isSameMonth($monthStart),
                'events_count' => $dayEvents->count(),
                'dot_count' => $dayEvents->count(),
                'dots' => $dots,
                'events' => $visibleEvents,
                'visible_dots' => $dots->take(3)->values(),
                'more_count' => max($dayEvents->count() - 3, 0),
            ];

            $day->addDay();
        }

       

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('F'),
                'year' => $monthStart->format('Y'),
                'month_start' => $monthStart->format('Y-m-d'),
                'month_end' => $monthEnd->format('Y-m-d'),
                'calendar_start' => $calendarStart->format('Y-m-d'),
                'calendar_end' => $calendarEnd->format('Y-m-d'),
                'calendar_days' => $calendarDays,
            ],
        ]);
    }

    private function eventStatusColor(?string $status): string
    {
        $status = strtolower(str_replace(['-', ' '], '_', $status ?? ''));

        return match ($status) {
            'confirmed' => '#2196F3',
            'in_progress', 'inprogress' => '#FF9800',
            'completed', 'done' => '#475B35',
            'cancelled', 'canceled' => '#F44336',
            'pending' => '#E19184',
            'accepted' => '#009688',
            'planning' => '#C63E4E',
            'declined' => '#620607',
            default => '#6b7280',
        };
    }
}