@extends('layouts.assistant')

@section('title', 'Vendor Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor_details.css') }}">
<style>
    body {
        background-color: #EFE7DA ; 
    }
</style>    
@endpush

@section('content')

<div class="vendor_details_page">
    <div class="vd_header">
        <a href="{{ url()->previous() }}" class="back_btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="title">Vendor Details</h1>
    </div>

    <div class="card">
        <p class="subheader">About</p>
        <p class="description">{{ $vendor->description }}</p>
    </div>

    <div class="card">
        <p class="subheader">Contact Information</p>
        <div class="card_info">
            @if($vendor->phoneNumber)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $vendor->phoneNumber) }}" target="_blank" class="info_link">
                <p class="info">
                    <i class="fab fa-whatsapp"></i>
                    <span class="info_text">
                        <span class="info_label">Phone</span>
                        <span class="info_value">{{ $vendor->phoneNumber }}</span>
                    </span>
                    <i class="fas fa-chevron-right info_arrow"></i>
                </p>
            </a>
            @endif

            @if($vendor->instagram)
            <a href="https://instagram.com/{{ $vendor->instagram }}" target="_blank" class="info_link">
                <p class="info">
                    <i class="fab fa-instagram"></i>
                    <span class="info_text">
                        <span class="info_label">Instagram</span>
                        <span class="info_value">{{ $vendor->instagram }}</span>
                    </span>
                    <i class="fas fa-chevron-right info_arrow"></i>
                </p>
            </a>
            @endif

            @if($vendor->email)
            <a href="mailto:{{ $vendor->email }}" class="info_link">
                <p class="info">
                    <i class="fas fa-envelope"></i>
                    <span class="info_text">
                        <span class="info_label">Email</span>
                        <span class="info_value">{{ $vendor->email }}</span>
                    </span>
                    <i class="fas fa-chevron-right info_arrow"></i>
                </p>
            </a>
            @endif

            @if($vendor->website)
            <a href="{{ $vendor->website }}" target="_blank" class="info_link">
                <p class="info">
                    <i class="fas fa-globe"></i>
                    <span class="info_text">
                        <span class="info_label">Website</span>
                        <span class="info_value">{{ $vendor->website }}</span>
                    </span>
                    <i class="fas fa-chevron-right info_arrow"></i>
                </p>
            </a>
            @endif
        </div>
    </div>

    <div class="card">
        <p class="subheader">Locations</p>
        @foreach($vendor->locations as $location)
            <p class="location_row"><i class="fas fa-map-marker-alt"></i>{{ $location }}</p>
        @endforeach
    </div>
</div>

@endsection