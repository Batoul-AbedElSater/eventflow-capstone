@extends('layouts.assistant')

@section('title', 'My Orders')

@push('styles')
<style>
    body {
        background-color: #EFE7DA;
         font-family: Georgia, 'Times New Roman', serif;
    }
    .orders-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 24px 36px;
    }
    
    /* Title */
    .orders-title {
        font-size: 28px;
        color: #620607;
        font-weight: 800;
        margin-bottom: 6px;
        text-align: left;
    }
    .orders-subtitle {
        font-size: 16px;
        color: #C63E4E;
        margin-bottom: 28px;
        text-align: left;
    }
    
    .order-card {
        background: white;
        border-radius: 16px;
        padding: 30px 34px;
        margin-bottom: 20px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .order-card .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .order-card .task-name {
        font-size: 22px;
        font-weight: 700;
        color: #2C3821;
    }
    .order-card .vendor-name {
        font-size: 18px;
        color: #C63E4E;
        font-weight: 600;
        margin-top: 4px;
    }
    .order-card .vendor-name i {
    color: #E19184;
}


    .order-card .order-price {
        font-size: 28px;
        font-weight: 800;
        color: #475B35;
        text-align: right;
    }
   .order-card .order-meta {
    display: flex;
    gap: 24px;
    font-size: 16px;
    color: #620607;
    margin-top: 10px;
}
  .order-card .order-meta i {
    color: #E19184;
    margin-right: 5px;
}
  .order-card .order-notes {
    font-size: 16px;
    color: #620607;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #f0ebe4;
}
.order-card .order-notes i {
    color: #E19184;
}

.order-card .task-name {
    font-size: 22px;
    font-weight: 700;
    color: #620607;
}
    .order-card .order-actions {
        margin-top: 14px;
        display: flex;
        gap: 10px;
    }

    .btn-view-order {
        padding: 8px 18px;
        background:transparent;
        color: #C63E4E;
        border: 2px solid #C63E4E;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }



    .btn-edit-order {
        padding: 8px 18px;
        background: #C63E4E;
        color: White;
        border: 2px solid #C63E4E;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-edit-order:hover {
        background: #E19184;
        border: 2px solid #E19184;
        color: white;
    }

    .btn-view-order:hover {
        background: #E19184;
        border: 2px solid #E19184;
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #aaa;
    }
    .empty-state i {
        font-size: 56px;
        color: #EFE7DA;
        margin-bottom: 16px;
    }
    .empty-state p {
        font-size: 19px;
        font-weight: 600;
        color: #bbb;
    }
    .header_title {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2.6rem;
    font-weight: 700;
    color: var(--vampire, #620607);
    letter-spacing: -0.5px;
    margin: 0;
    margin-bottom: 14px;
    margin-top: 4px;
}
</style>
@endpush

@section('content')

<div class="orders-container">
    
 <h2 class="header_title" style="margin-bottom: 25px;">
    My Orders
</h2>
<p class="orders-subtitle">Track all your vendor orders in one place</p>

    
    @forelse($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="task-name">{{ $order->task->title }}</div>
                    <div class="vendor-name">
                        <i class="fas fa-store"></i> {{ $order->vendor->name }}
                    </div>
                    @if($order->task->event)
                       <div style="font-size: 13px; color: #620607; margin-top: 8px; margin-left: 3px;">
    <i class="fas fa-calendar-alt" style="color: #E19184;"></i> {{ $order->task->event->name }}
</div>
                    @endif
                </div>
                <div class="order-price">${{ number_format($order->price, 2) }}</div>
            </div>
            
            @if($order->notes)
                <div class="order-notes">
                    <i class="fas fa-sticky-note"></i> {{ $order->notes }}
                </div>
            @endif
            
            <div class="order-meta">
                <span><i class="fas fa-calendar"></i> {{ $order->created_at->format('M d, Y') }}</span>
                <span><i class="fas fa-tag"></i> {{ ucfirst($order->vendor->category) }}</span>
            </div>
            
            <div class="order-actions">

                 <a href="{{ route('assistant.tasks.vendors', $order->task_id) }}" class="btn-view-order">
                    <i class="fas fa-eye"></i> View Vendors
                </a>
                <a href="{{ route('assistant.vendor.order', ['task' => $order->task_id, 'vendor' => $order->vendor_id]) }}" class="btn-edit-order">
                    <i class="fas fa-edit"></i> Edit Order
                </a>
               
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <p>No orders placed yet.</p>
        </div>
    @endforelse
</div>

@endsection