@extends('layouts.planner')

@section('title', 'Budget AI Universe - ' . $event->name)

@section('content')
@php
    $hasDraft = !is_null($event->aiBudgetDraft);
    $hasBudget = !is_null($event->budget);
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&family=Manrope:wght@400;600;700&display=swap');

    .hub {
        --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --green-dark: #2C3821;
        font-family: 'Manrope', sans-serif;
        min-height: 85vh;
        padding: 24px;
        border-radius: 20px;
        background: linear-gradient(130deg, var(--cream) 0%, #f9f4eb 45%, #f3f7eb 100%);
        color: var(--vampire);
    }
    .hub.dark {
        background: linear-gradient(130deg, #1f0b0e 0%, #2a1218 45%, #0f1a14 100%);
        color: #fffaf2;
    }
    .hub.dark .card, .hub.dark .stage, .hub.dark .hero-panel, .hub.dark .stepline {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.16);
        color: #fffaf2;
    }
    .hub.dark h1,
    .hub.dark h2,
    .hub.dark h3,
    .hub.dark h4,
    .hub.dark p,
    .hub.dark .step p,
    .hub.dark .title p,
    .hub.dark .status {
        color: #fffaf2;
    }
    .hub.dark .wait {
        background: rgba(198, 62, 78, 0.18);
        color: #fffaf2;
    }
    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .title h1 {
        margin: 0;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 40px;
        letter-spacing: 0.2px;
    }
    .title p { margin: 6px 0 0; opacity: 0.9; }
    .mode-toggle {
        border: 0;
        border-radius: 10px;
        padding: 9px 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--vampire);
        color: #fff;
    }
    .hub.dark .mode-toggle {
        background: var(--amnesiac);
        color: var(--green-dark);
    }
    .hero {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }
    .hero-panel {
        border-radius: 16px;
        padding: 18px;
        background: var(--white);
        border: 1px solid rgba(98, 6, 7, 0.12);
        box-shadow: 0 12px 24px rgba(98, 6, 7, 0.08);
    }
    .hero-panel h2 {
        margin: 0 0 8px;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 28px;
    }
    .icons-wrap {
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .ai-big {
        font-size: 84px;
        color: var(--berry);
        filter: drop-shadow(0 12px 18px rgba(198, 62, 78, 0.28));
        animation: aiPulse 2.2s infinite;
    }
    .money {
        font-size: 52px;
        color: var(--green);
        animation: moneyFloat 2.6s ease-in-out infinite;
    }
    @keyframes aiPulse { 0%,100% { transform: scale(1);} 50% { transform: scale(1.07);} }
    @keyframes moneyFloat { 0%,100% { transform: translateY(0);} 50% { transform: translateY(-8px);} }

    .stepline {
        margin-bottom: 12px;
        border-radius: 12px;
        border: 1px solid rgba(98, 6, 7, 0.14);
        background: var(--white);
        padding: 10px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }
    .step {
        border-radius: 10px;
        padding: 10px;
        background: rgba(225, 145, 132, 0.12);
    }
    .step.active { background: rgba(71, 91, 53, 0.16); }
    .step h4 { margin: 0 0 5px; font-size: 14px; }
    .step p { margin: 0; font-size: 12px; opacity: 0.9; }

    .grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .card {
        border-radius: 14px;
        padding: 16px;
        background: var(--white);
        border: 1px solid rgba(98, 6, 7, 0.12);
        box-shadow: 0 10px 22px rgba(98, 6, 7, 0.06);
    }
    .card h3 {
        margin: 0 0 8px;
        font-family: 'Space Grotesk', sans-serif;
    }
    .card p { margin: 0 0 12px; opacity: 0.9; }
    .btn {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 700;
    }
    .btn-ai { background: linear-gradient(135deg, var(--berry), var(--vampire)); color: #fff; }
    .btn-editor { background: linear-gradient(135deg, var(--green), var(--green-dark)); color: #fff; }
    .btn-disabled { background: #d8d1c6; color: #7b7368; pointer-events: none; }
    .status {
        margin-top: 9px;
        display: inline-flex;
        gap: 6px;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 700;
    }
    .ok { background: #e6f3e9; color: var(--green-dark); }
    .wait { background: #f4e9ec; color: var(--vampire); }

    @media (max-width: 1020px) {
        .hero, .grid, .stepline { grid-template-columns: 1fr; }
    }
</style>

<div class="hub" id="budget-hub">
    <div class="topbar">
        <div class="title">
            <h1>Welcome to Budget AI Universe</h1>
            <p>{{ $event->name }} · {{ $event->eventType->name ?? 'Event' }} · Intelligent budget planning and execution</p>
        </div>
        <button type="button" class="mode-toggle" id="mode-toggle">
            <i class="fas fa-circle-half-stroke"></i>
            Toggle Dark / Light
        </button>
    </div>

    <div class="hero">
        <div class="hero-panel">
            <h2>What you can do in this Universe</h2>
            <p>Generate AI budget intelligence, convert it into planner-controlled records, and finalize a professional event budget dashboard.</p>
        </div>
        <div class="hero-panel">
            <div class="icons-wrap">
                <i class="fas fa-robot ai-big"></i>
                <i class="fas fa-dollar-sign money"></i>
                <i class="fas fa-dollar-sign money"></i>
            </div>
        </div>
    </div>

    <div class="stepline">
        <div class="step {{ $hasDraft ? 'active' : '' }}">
            <h4><i class="fas fa-robot"></i> AI Forecast Studio</h4>
            <p>Create budget suggestions, warnings, and planner questions.</p>
        </div>
        <div class="step {{ $hasBudget ? 'active' : '' }}">
            <h4><i class="fas fa-right-left"></i> Planning Conversion</h4>
            <p>Import AI draft into editable planning records.</p>
        </div>
        <div class="step {{ $hasBudget ? 'active' : '' }}">
            <h4><i class="fas fa-table"></i> Execution Console</h4>
            <p>Edit, track, and finalize budget operations.</p>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>AI Forecast Studio</h3>
            <p>Generate and review AI budget ideas for categories, totals, and planning insights.</p>
            <a href="{{ route('planner.events.budget.draft', $event) }}" class="btn btn-ai">
                <i class="fas fa-arrow-right"></i> Open AI Forecast Studio
            </a>
            <div class="status {{ $hasDraft ? 'ok' : 'wait' }}">
                <i class="fas {{ $hasDraft ? 'fa-check-circle' : 'fa-clock' }}"></i>
                {{ $hasDraft ? 'Draft exists' : 'No draft yet' }}
            </div>
        </div>

        <div class="card">
            <h3>Execution Budget Console</h3>
            <p>Use advanced tables and controls to manage costs, status, and planner decisions.</p>
            <a href="{{ $hasBudget ? route('planner.events.budget.editor', $event) : '#' }}" class="btn {{ $hasBudget ? 'btn-editor' : 'btn-disabled' }}">
                <i class="fas fa-arrow-right"></i> {{ $hasBudget ? 'Open Execution Console' : 'Import draft first' }}
            </a>
            <div class="status {{ $hasBudget ? 'ok' : 'wait' }}">
                <i class="fas {{ $hasBudget ? 'fa-check-circle' : 'fa-lock' }}"></i>
                {{ $hasBudget ? 'Editor ready' : 'Editor locked' }}
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const key = 'budget_mode';
        const hub = document.getElementById('budget-hub');
        const toggle = document.getElementById('mode-toggle');
        if (!hub || !toggle) return;

        function setMode(mode) {
            hub.classList.toggle('dark', mode === 'dark');
            localStorage.setItem(key, mode);
        }

        const savedMode = localStorage.getItem(key) || 'light';
        setMode(savedMode);

        toggle.addEventListener('click', function () {
            const next = hub.classList.contains('dark') ? 'light' : 'dark';
            setMode(next);
        });
    })();
</script>
@endsection
