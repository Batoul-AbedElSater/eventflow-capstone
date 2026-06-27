@extends('layouts.planner')

@section('title','Vendors')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
@endpush

@section("content")

<div class="vendors_page">
    <h1 class="header_title">Vendors</h1>

    <div class="vendors_headr">
        <div class="search_bar">
            <i class="fas fa-search"></i>
            <input class="search_input" id="search_bar" placeholder="Search vendors....">
        </div>
        <button class="header_heart_btn" onclick="window.location='{{ route('planner.events.vendors.favorites', $event->id) }}'">
            Favorite Vendors <i class="fas fa-heart"></i>
        </button>
    </div>

    <div class="category_btn">
        <button class="fltr_btn_active" data-category="all">All</button>
        <button class="fltr_btn" data-category="catering">Catering</button>
        <button class="fltr_btn" data-category="photography">Photography</button>
        <button class="fltr_btn" data-category="decoration">Decoration</button>
        <button class="fltr_btn" data-category="music">Music</button>
        <button class="fltr_btn" data-category="venue">Venue</button>
    </div>

    <hr class="vendors-divider">

    <div class="vendor_grid" id="vendorsGrid">
        @php
            $favoriteIds = $event->vendors()
                ->wherePivot('is_favorite', true)
                ->pluck('vendors.id')
                ->toArray();
        @endphp

        @forelse ($vendors as $vendor)
            @php
                $orderCount = \App\Models\VendorOrder::where('vendor_id', $vendor->id)
                    ->whereHas('task', function($q) use ($event) {
                        $q->where('event_id', $event->id);
                    })->count();
            @endphp
            <div class="vendor_card" data-category="{{ $vendor->category }}">
                <div class="vendor_card_top">
                    <img src="{{ asset($vendor->imageIcon) }}" alt="{{ $vendor->name }}" class="vendor_img">
                    <div class="vendor_info">
                        <h3 class="vendor_name">{{ strtoupper($vendor->name) }}</h3>
                        <span class="vendor_category">{{ ucfirst($vendor->category) }}</span>
                        <div class="vendor_rating">
                            <i class="fas fa-star"></i>
                            <span>{{ $vendor->rating }}</span>
                        </div>
                        
                        @if($orderCount > 0)
                            <span style="display: inline-block; background: var(--coral, #E19184); color: white; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-top: 4px;">
                                <i class="fas fa-shopping-cart"></i> {{ $orderCount }} order{{ $orderCount > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="vendor_card_actions">
                    <form method="POST" action="{{ route('planner.events.vendors.toggleFavorite', [$event->id, $vendor->id]) }}">
                        @csrf
                        <button type="submit" class="card_heart_btn {{ in_array($vendor->id, $favoriteIds) ? 'favorited' : '' }}">
                            <i class="{{ in_array($vendor->id, $favoriteIds) ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    </form>
                    <button class="btn_view" onclick="window.location='{{ route('planner.events.vendors.show', [$event->id, $vendor->id]) }}'">View Details</button>
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $vendor->phoneNumber) }}" target="_blank" class="book_link">
                        <button class="btn_book">Book Vendor</button>
                    </a>
                </div>
            </div>
        @empty
            <div class="no_vendors">
                <i class="fas fa-store-slash"></i>
                <p>No vendors found.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/vendor.js') }}"></script>
@endpush