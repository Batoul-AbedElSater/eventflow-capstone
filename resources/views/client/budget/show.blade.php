@extends('layouts.client')

@section('title', 'Budget - ' . $event->name)

@section('content')
<div class="budget-container">
    
    <!-- Header -->
    <div class="page-header">
        <div class="header-left">
            <a href="{{ route('client.events.show', $event->id) }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Event
            </a>
            <h1>Budget Overview</h1>
            <p class="event-name">{{ $event->name }}</p>
        </div>
    </div>
    <div class="header-left">
    <a href="{{ route('client.dashboard') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Budget Summary Cards -->
    <div class="budget-summary">
        <div class="summary-card total">
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="card-content">
                <h3>${{ number_format($totalBudget, 2) }}</h3>
                <p>Total Budget</p>
            </div>
        </div>

        <div class="summary-card spent">
            <div class="card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="card-content">
                <h3>${{ number_format($totalSpent, 2) }}</h3>
                <p>Total Spent</p>
            </div>
        </div>

        <div class="summary-card remaining">
            <div class="card-icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="card-content">
                <h3>${{ number_format($remaining, 2) }}</h3>
                <p>Remaining</p>
            </div>
        </div>

        <div class="summary-card percentage">
            <div class="card-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="card-content">
                <h3>{{ $percentageSpent }}%</h3>
                <p>Budget Used</p>
            </div>
        </div>
    </div>

    <!-- Overall Progress Bar -->
    <div class="overall-progress-card">
        <div class="progress-header">
            <h3>Overall Budget Progress</h3>
            <span class="progress-label">{{ $percentageSpent }}% used</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $percentageSpent }}%">
                <span class="progress-text">${{ number_format($totalSpent, 0) }} / ${{ number_format($totalBudget, 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Budget Categories -->
    <div class="categories-section">
        <div class="section-header">
            <h2>Budget Categories</h2>
            <p class="subtitle">Managed by your event planner</p>
        </div>

        @if($event->budgetCategories->count() > 0)
            <div class="categories-grid">
                @foreach($event->budgetCategories as $category)
                    @php
                        $categoryPercentage = $category->allocated_amount > 0 
                            ? round(($category->spent_amount / $category->allocated_amount) * 100, 1) 
                            : 0;
                        $statusClass = $categoryPercentage >= 100 ? 'over-budget' : ($categoryPercentage >= 90 ? 'near-limit' : 'on-track');
                    @endphp
                    
                    <div class="category-card {{ $statusClass }}">
                        <div class="category-header">
                            <div class="category-icon">
                                @switch($category->name)
                                    @case('Venue') <i class="fas fa-building"></i> @break
                                    @case('Catering') <i class="fas fa-utensils"></i> @break
                                    @case('Photography') <i class="fas fa-camera"></i> @break
                                    @case('Decorations') <i class="fas fa-palette"></i> @break
                                    @case('Entertainment') <i class="fas fa-music"></i> @break
                                    @case('Transportation') <i class="fas fa-car"></i> @break
                                    @case('Attire') <i class="fas fa-tshirt"></i> @break
                                    @default <i class="fas fa-dollar-sign"></i>
                                @endswitch
                            </div>
                            <div class="category-info">
                                <h4>{{ $category->name }}</h4>
                                @if($category->description)
                                    <p class="category-description">{{ $category->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="category-amounts">
                            <div class="amount-row">
                                <span class="amount-label">Allocated:</span>
                                <span class="amount-value">${{ number_format($category->allocated_amount, 2) }}</span>
                            </div>
                            <div class="amount-row">
                                <span class="amount-label">Spent:</span>
                                <span class="amount-value spent">${{ number_format($category->spent_amount, 2) }}</span>
                            </div>
                            <div class="amount-row">
                                <span class="amount-label">Remaining:</span>
                                <span class="amount-value remaining">
                                    ${{ number_format($category->allocated_amount - $category->spent_amount, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="category-progress">
                            <div class="progress-header">
                                <span class="progress-percentage">{{ $categoryPercentage }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill {{ $statusClass }}" style="width: {{ min($categoryPercentage, 100) }}%"></div>
                            </div>
                        </div>

                        @if($categoryPercentage >= 100)
                            <div class="budget-alert over">
                                <i class="fas fa-exclamation-triangle"></i>
                                Over budget by ${{ number_format($category->spent_amount - $category->allocated_amount, 2) }}
                            </div>
                        @elseif($categoryPercentage >= 90)
                            <div class="budget-alert warning">
                                <i class="fas fa-exclamation-circle"></i>
                                Approaching budget limit
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>No Budget Categories Yet</h3>
                <p>Your event planner will add budget categories and track spending for you.</p>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Budget Management</strong>
            <p>Your event planner manages all budget categories and spending. You can view progress here at any time.</p>
        </div>
    </div>

</div>

<link rel="stylesheet" href="{{ asset('css/client-budget.css') }}">
@endsection