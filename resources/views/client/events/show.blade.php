@extends('layouts.client')

@section('title', 'Event Details')

@section('content')
<div class="coming-soon-container">
    
    <!-- Success Message -->
    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <h2>{{ session('success') }}</h2>
        </div>
    @endif

    <div class="coming-soon-card">
        <i class="fas fa-calendar-check"></i>
        <h2>Event Details Page</h2>
        <p>Coming Soon!</p>
        <p class="note">We're working on this feature part by part.</p>
        
        <a href="{{ route('client.dashboard') }}" class="btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

</div>

<style>
.coming-soon-container {
    max-width: 600px;
    margin: 100px auto;
    text-align: center;
}

.success-message {
    background: rgba(126, 211, 33, 0.1);
    border: 2px solid #7ED321;
    color: #5FA119;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.success-message i {
    font-size: 32px;
}

.success-message h2 {
    font-size: 20px;
    margin: 0;
}

.coming-soon-card {
    background: var(--white);
    border-radius: 16px;
    padding: 60px 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.coming-soon-card i {
    font-size: 80px;
    color: var(--beige);
    margin-bottom: 20px;
}

.coming-soon-card h2 {
    font-size: 28px;
    color: var(--onyx);
    margin-bottom: 10px;
}

.coming-soon-card p {
    font-size: 18px;
    color: var(--gray);
    margin-bottom: 10px;
}

.coming-soon-card .note {
    font-size: 14px;
    color: var(--gray);
    margin-bottom: 30px;
}
</style>
@endsection