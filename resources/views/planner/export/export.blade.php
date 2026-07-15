<h1>EVENT PLAN</h1>

<p>Event: {{ $event->name }}</p>
<p>Date: {{ $event->start_date }}</p>
<p>Location: {{ $event->location_text }}</p>
<p>Guests: {{ $event->guest_estimate }}</p>

<h2>BUDGET SUMMARY</h2>

@foreach($event->budget->items as $item)

<p>
{{ $item->category }}
.................
${{ $item->estimated_cost }}
</p>

@endforeach

<hr>

<p>
Total Budget:
${{ $event->budget->total_client_budget }}
</p>

<h2>RECOMMENDATIONS</h2>

<p>
{{ $event->budget->planner_notes }}
</p>
