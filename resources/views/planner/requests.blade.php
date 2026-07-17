@extends('layouts.planner')

@section('title', 'Event Requests')

@section('content')
<div class="requests-page-container">

    {{-- Page Header --}}
    <div class="requests-page-header">
        <div class="header-left">
            <h1> Event Requests</h1>
        </div>
        <div class="header-stats">
            <div class="header-stat-card pending">
                <div class="stat-info">
                    <strong>{{ $pendingRequests->count() }}</strong>
                    <span>Pending</span>
                </div>
            </div>
            <div class="header-stat-card accepted">
                <div class="stat-info">
                    <strong>{{ $acceptedToday ?? 0 }}</strong>
                    <span>Accepted Today</span>
                </div>
            </div>
            <div class="header-stat-card declined">
                <div class="stat-info">
                    <strong>{{ $declinedToday ?? 0 }}</strong>
                    <span>Declined Today</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}

            <div class="requests-filter-bar">
                <div class="filter-left">
                    <a href="{{ route('planner.requests') }}"
                    class="filter-btn {{ !request('filter') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i> All Requests
                    </a>
                    <a href="{{ route('planner.requests', ['filter' => 'high-budget']) }}"
                    class="filter-btn {{ request('filter') === 'high-budget' ? 'active' : '' }}">
                        <i class="fas fa-dollar-sign"></i> High Budget
                    </a>
                </div>
            </div>

    @if($pendingRequests->count() > 0)
        {{-- Requests Grid --}}
        <div class="requests-creative-grid">
            @foreach($pendingRequests as $request)
                <div class="request-creative-card" data-request-id="{{ $request->id }}">

                    {{-- Card Header --}}
                    <div class="request-card-header">

                        <div class="request-card-title">
                            <h3>{{ $request->name }}</h3>
                        </div>
                        <div class="request-urgency">
                          @php
                                $daysUntil = (int) now()->diffInDays($request->start_date, false);
                                $urgency = $daysUntil <= 30 ? 'urgent' : ($daysUntil <= 60 ? 'medium' : 'low');
                           @endphp
                            <span class="urgency-badge {{ $urgency }}">
                                <i class="fas fa-clock"></i>
                                {{ $daysUntil }} {{ $daysUntil === 1 ? 'day' : 'days' }} away
                            </span>
                        </div>
                    </div>

                    {{-- Client Info --}}
                    <div class="request-client-info">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($request->client->name) }}&background=E19184&color=fff"
                             alt="{{ $request->client->name }}"
                             class="client-avatar">
                        <div class="client-details">
                            <strong>{{ $request->client->name }}</strong>
                            <span>{{ $request->client->email }}</span>
                        </div>
                    </div>

                    {{-- Event Details Grid --}}
                    <div class="request-detail-grid">
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <span class="detail-label">Date</span>
                                <strong>{{ $request->start_date->format('M d, Y') }}</strong>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <span class="detail-label">Location</span>
                                <strong>{{ Str::limit($request->location_text, 25) }}</strong>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <span class="detail-label">Guests</span>
                                <strong>{{ $request->guest_estimate }}</strong>
                            </div>
                        </div>
                        <div class="detail-item highlight">
                            <i class="fas fa-dollar-sign"></i>
                            <div>
                                <span class="detail-label">Budget</span>
                                <strong>{{ number_format($request->budget_overall, 0) }}</strong>
                             </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($request->description)
                        <div class="request-description">
                            <i class="fas fa-quote-left"></i>
                            <p>{{ Str::limit($request->description, 150) }}</p>
                        </div>
                    @endif

                    {{-- Timeline Info --}}
                    <div class="request-timeline">
                        <i class="fas fa-clock"></i>
                        <span>Requested {{ $request->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Expandable Details --}}
                    <div class="request-expandable" id="details-{{ $request->id }}" style="display: none;">
                        <div class="expandable-content">
                            <h5><i class="fas fa-clipboard-list"></i> Additional Information</h5>

                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Event Duration:</span>
                                    <span class="info-value">
                                        @if($request->end_date)
                                            {{ $request->start_date->diffInHours($request->end_date) }} hours
                                        @else
                                            Not specified
                                        @endif
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Special Requirements:</span>
                                    <span class="info-value">{{ $request->special_requirements ?? 'None' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Action Buttons --}}

                            <div class="request-main-actions">
                                <form method="POST" action="{{ route('planner.requests.accept', $request->id) }}" class="action-form accept-form">
                                    @csrf
                                    <button type="submit" class="btn-accept-creative">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Accept Request</span>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('planner.requests.decline', $request->id) }}" class="action-form decline-form">
                                    @csrf
                                    <button type="submit" class="btn-decline-creative" onclick="return confirm('Are you sure you want to decline this event?')">
                                        <i class="fas fa-times-circle"></i>
                                        <span>Decline</span>
                                    </button>
                                </form>
                            </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-container">
            {{ $pendingRequests->links() }}
        </div>

    @else
        {{-- Empty State --}}
        <div class="empty-state-creative">
<div class="empty-icon"><i class="fas fa-calendar-check"></i></div>
            <h3>All Caught Up!</h3>
            <p class="empty-subtext">New requests will appear here when clients submit them.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function toggleDetails(id) {
    const details = document.getElementById('details-' + id);
    if (details.style.display === 'none') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}
// Filter functionality
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        // Add filter logic here
    });
});
</script>
@endpush
