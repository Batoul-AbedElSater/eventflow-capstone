@extends('layouts.assistant')

@section('title', 'Place Order')

@push('styles')
<style>
    body {
        background-color: #EFE7DA; 
        font-family: Georgia, 'Times New Roman', serif;
    }
    .order-form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px;
    }
    
    /* Card wrapper */
    .order-card {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.06);
    }
    
    .order-form-container h2 {
        font-size: 26px;
        color: #620607;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .order-form-container .subtitle {
        color: #C63E4E;
        font-size: 15px;
        margin-bottom: 24px;
    }
    .order-info {
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0ebe4;
    }
    .order-info p {
        margin: 6px 0;
        font-size: 18px;
        color: #620607;
        font-weight: 500;
    }
    .order-info strong {
        color: #620607;
        font-weight: 800;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        color: #620607;
        margin-bottom: 6px;
        font-size: 16px;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 14px;
        border: 2px solid #EFE7DA;
        border-radius: 10px;
        font-size: 16px;
       font-family: Georgia, 'Times New Roman', serif;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #E19184;
    }
    .btn-submit {
        width: 100%;
        padding: 16px;
        background: #C63E4E;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 8px;
    }
    .btn-submit:hover {
        background: #A8323A;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #C63E4E;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
    }
    .existing-order {
        background: #fdf0ed;
        border: 1px solid #E19184;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #C63E4E;
    }
</style>
@endpush

@section('content')



<div class="order-form-container">
    
    <div class="order-card">
       <h2 style="padding-bottom: 10px; font-size: 34px;">Place Order</h2>
       
        <p class="subtitle">Fill in the order details for this vendor</p>
        
        @if($existingOrder)
            <div class="existing-order">
                <i class="fas fa-info-circle"></i> 
                You already placed an order for this vendor. You can update it below.
            </div>
        @endif
        
        {{-- Order Info --}}
        <div class="order-info">
            <p><strong>Task:</strong> {{ $task->title }}</p>
            <p><strong>Vendor:</strong> {{ $vendor->name }}</p>
            <p><strong>Category:</strong> {{ ucfirst($vendor->category) }}</p>
        </div>
        
        {{-- Form --}}
        <form method="POST" action="{{ route('assistant.vendor.order.submit', ['task' => $task->id, 'vendor' => $vendor->id]) }}">
            @csrf
            
            <div class="form-group">
                <label for="price">Price ($) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" 
                       value="{{ old('price', $existingOrder->price ?? '') }}" 
                       placeholder="0.00" required>
            </div>
            
            <div class="form-group">
                <label for="notes">Order Notes</label>
                <textarea id="notes" name="notes" rows="4" 
                          placeholder="Make your order here">{{ old('notes', $existingOrder->notes ?? '') }}</textarea>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-shopping-cart"></i> 
                {{ $existingOrder ? 'Update Order' : 'Place Order' }}
            </button>
        </form>
    </div>
    
</div>

@endsection