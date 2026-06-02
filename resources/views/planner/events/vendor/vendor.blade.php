@extends('layouts.planner')

@section('title','Vendors')

@push('styles')
<link rel="stylesheet"  href="{{ asset('css/vendor.css')}}">
@endpush

@section("content")

<div class="vendors_page">

    <div class="vendors_headr">
    <h1 class="header_title">Vendors</h1>
<button class=" header_heart_btn">
    <i class="fas fa-heart"></i>
</button>
    </div>

    <div class="search_bar">
        <i class="fas fa-search"></i>
        <input class="search_input"  id="search_bar" placeholder="Search vendors....">
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
@forelse ($vendors as $vendor)
<div class="vendor_card" data-category="{{ $vendor->category}}">
    <div class ="vendor_card_top">
        <img src="{{ asset($vendor->imageIcon) }}" alt="{{$vendor->name  }}" class="vendor_img">

        <div class="vendor_info">
            <h3 class="vendor_name">{{ strtoupper($vendor->name) }}</h3>

        <span class="vendor_category">{{ ucfirst($vendor->category) }}</span>
      <div class="vendor_rating">
    <i class="fas fa-star"></i>
    <span>{{ $vendor->rating }}</span>
      </div>
    </div>
    </div>
    <div class="vendor_card_actions">
        <button class="card_heart_btn">
    <i class="far fa-heart"></i>
</button>
        <button class="btn_view">View Details</button>
         <button class="btn_book">Book Vendor</button>
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


