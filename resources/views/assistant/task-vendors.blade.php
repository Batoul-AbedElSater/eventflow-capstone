@extends('layouts.assistant')

@section('title', 'Task Vendors')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
<style>
    body {
        background-color: #EFE7DA ; 
    }
.btn_view {
    background: transparent;
    color: #C63E4E;
    border: 2px solid #C63E4E;
    text-align: center;
    text-decoration: none !important;
    display: inline-block;
    padding: 8px 5px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
.btn_book {
    background: var(#C63E4E);
    color: white;
    border: none;
    text-align: center;
    text-decoration: none !important;
    display: inline-block;
    padding: 8px 5px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
</style>
@endpush

@section('content')

<div class="vendors_page" style="padding-left: 20px;">
    
    {{-- Title --}}
    <h2 style="font-size: 40px; color: var(--vampire, #620607); font-weight: 800; margin-bottom: 40px; padding-left: 10px;">
        Assigned Vendors
    </h2>

    {{-- Vendor Grid --}}
    <div class="vendor_grid" id="vendorsGrid">
       @forelse ($task->vendors as $vendor)
    @php
        $order = \App\Models\VendorOrder::where('task_id', $task->id)
            ->where('vendor_id', $vendor->id)
            ->where('assistant_id', auth()->id())
            ->first();
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
                
                @if($order)
                    <span style="font-size: 12px; color: var(--green); font-weight: 600;">
                        📋 Order: ${{ number_format($order->price, 2) }}
                    </span>
                @endif
            </div>
        </div>
        
        <div class="vendor_card_actions">
            <a href="{{ route('assistant.vendor.show', $vendor->id) }}" class="btn_view">View Details</a>
            <a href="{{ route('assistant.vendor.order', ['task' => $task->id, 'vendor' => $vendor->id]) }}" class="btn_book">
                {{ $order ? 'Edit Order' : 'Place Order' }}
            </a>
        </div>
    </div>
@empty
            <div class="no_vendors">
                <i class="fas fa-store-slash"></i>
                <p>No vendors assigned to this task yet.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection