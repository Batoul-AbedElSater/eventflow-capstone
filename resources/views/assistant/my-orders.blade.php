@extends('layouts.assistant')

@section('title', 'My Orders')

@push('styles')
<style>
    :root {
        --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --green: #475B35;
        --green-dark: #2C3821;
    }

    body {
        background-color: var(--cream);
    }

    /* ===== WELCOME BANNER (matches My Tasks) ===== */
    .orders-banner {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--coral) 0%, var(--coral) 100%);
        color: var(--vampire);
        padding: 42px 48px;
        border-radius: 20px;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        box-shadow: 0 12px 32px rgba(98,6,7,0.22);
        font-family: Georgia, 'Times New Roman', serif;
    }
    .orders-banner::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.14);
        pointer-events: none;
    }
    .orders-banner::after {
        content: '';
        position: absolute;
        bottom: -70px;
        right: -20px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(98,6,7,0.16);
        pointer-events: none;
    }
    .orders-banner .banner-left {
        position: relative;
        z-index: 1;
    }
    .orders-banner h2 {
        font-size: 40px;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin: 0 0 10px;
        text-shadow: 0 2px 10px rgba(98,6,7,0.15);
    }
    .orders-banner p {
        font-size: 17px;
        font-weight: 500;
        opacity: 0.92;
        margin: 0;
    }

    .banner-stats {
        position: relative;
        z-index: 1;
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }
    .banner-stat {
        background: white;
        border-radius: 16px;
        padding: 18px 28px;
        text-align: center;
        min-width: 130px;
        box-shadow: 0 6px 18px rgba(98,6,7,0.16);
        border: 2px solid var(--vampire);
    }
    .banner-stat .banner-stat-value {
        font-size: 36px;
        font-weight: 800;
        color: var(--vampire);
        line-height: 1;
    }
    .banner-stat .banner-stat-label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--berry);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-top: 7px;
    }
    .banner-stat.spent .banner-stat-value { color: var(--green); }

    /* ===== ORDER CARDS ===== */
    .order-card {
        background: white;
        border-radius: 18px;
        padding: 26px 30px;
        margin-bottom: 16px;
        box-shadow: 0 3px 14px rgba(98,6,7,0.06);
        border: 1.5px solid #f2ece4;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(98,6,7,0.10);
    }
    .order-card .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
        gap: 16px;
    }
    .order-card .task-name {
        font-size: 21px;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: var(--green-dark);
    }
    .order-card .vendor-name {
        font-size: 15px;
        font-weight: 600;
        color: var(--green-dark);
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .order-card .vendor-name i {
        color: var(--coral);
        font-size: 15px;
    }
    .order-card .event-tag {
        font-size: 14px;
        font-weight: 600;
        color: var(--green-dark);
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .order-card .event-tag i {
        color: var(--coral);
    }
    .order-card .order-price {
        font-size: 28px;
        font-weight: 800;
        color: var(--green);
        text-align: right;
        white-space: nowrap;
    }
    .order-card .order-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 22px;
        font-size: 14.5px;
        font-weight: 600;
        color: var(--green-dark);
        margin-top: 12px;
    }
    .order-card .order-meta i {
        color: var(--coral);
        margin-right: 5px;
    }
    .order-card .order-notes {
        font-size: 14.5px;
        font-weight: 500;
        color: var(--green-dark);
        background: #faf7f3;
        border-radius: 10px;
        padding: 12px 16px;
        margin-top: 12px;
        border-left: 3px solid var(--coral);
        display: flex;
        gap: 9px;
        align-items: flex-start;
    }
    .order-card .order-notes i {
        color: var(--coral);
        font-size: 15px;
        margin-top: 1px;
    }
    .order-card .order-actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-view-order {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        background: var(--coral);
        color: white;
        border: none;
        border-radius: 20px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-view-order:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(225, 145, 132, 0.45);
        color: white;
    }
    .btn-edit-order {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 22px;
        background: linear-gradient(135deg, var(--berry), var(--vampire));
        color: white;
        border: none;
        border-radius: 22px;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(98,6,7,0.2);
    }
    .btn-edit-order:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(98,6,7,0.32);
        color: white;
    }
    .btn-delete-order {
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        margin-left: auto;
    }
    .btn-delete-order i {
        color: #cbbfae;
        font-size: 18px;
        transition: color 0.2s;
    }
    .btn-delete-order:hover i {
        color: var(--vampire);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #aaa;
    }
    .empty-state i {
        font-size: 56px;
        color: var(--cream);
        margin-bottom: 16px;
    }
    .empty-state p {
        font-size: 19px;
        font-weight: 600;
        color: #bbb;
    }
</style>
@endpush

@section('content')

    <div class="orders-banner">
        <div class="banner-left">
            <h2>My Orders</h2>
            <p>Track all your vendor orders in one place.</p>
        </div>
        <div class="banner-stats">
            <div class="banner-stat">
                <div class="banner-stat-value">{{ $orders->count() }}</div>
                <div class="banner-stat-label">Total Orders</div>
            </div>
            <div class="banner-stat spent">
                <div class="banner-stat-value">${{ number_format($orders->sum('price'), 0) }}</div>
                <div class="banner-stat-label">Total Spent</div>
            </div>
        </div>
    </div>

    @forelse($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="task-name">{{ $order->task->title }}</div>
                    <div class="vendor-name">
                        <i class="fas fa-store"></i> {{ $order->vendor->name }}
                    </div>
                    @if($order->task->event)
                        <div class="event-tag">
                            <i class="fas fa-calendar-alt"></i> {{ $order->task->event->name }}
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
<form method="POST" action="{{ route('assistant.orders.delete', $order->id) }}" onsubmit="return confirm('Delete this order?')" style="margin-left: auto;">
    @csrf
    <button type="submit" class="btn-delete-order">
        <i class="fas fa-trash"></i>
    </button>
</form>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <p>No orders placed yet.</p>
        </div>
    @endforelse

@endsection