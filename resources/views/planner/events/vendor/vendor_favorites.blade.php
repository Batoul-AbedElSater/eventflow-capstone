@extends("layouts.planner")

@section('title','Favorite Vendors')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor_favorites.css') }}">
@endpush

@section('content')

<div class="favorite vendors_page">

    <div class="vd_header">
      <a href="{{ route('planner.events.vendors.index', $event->id) }}" class="back_btn">
    <i class="fas fa-arrow-left"></i>
</a>
        <h1 class="title">My Favorites</h1>
    </div>

    <div class="fav_list">
        @forelse($vendors as $vendor)
        <div class="fav_card">

            <form method="POST" action="{{ route('planner.events.vendors.removeFavorite', [$event->id, $vendor->id]) }}">
                @csrf
                <button type="submit" class="fav_heart_btn">
                    <i class="fas fa-heart"></i>
                </button>
            </form>

            <a href="{{ route('planner.events.vendors.show', [$event->id, $vendor->id]) }}" class="fav_link">
                <div class="fav_info">
                    <p class="fav_name">{{ strtoupper($vendor->name) }}</p>
                    <p class="fav_category">{{ ucfirst($vendor->category) }}</p>
                </div>
                <i class="fas fa-chevron-right fav_arrow"></i>
            </a>

        </div>
        @empty
        <div class="no_favorites">
            <i class="far fa-heart"></i>
            <p>No favorites yet</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
