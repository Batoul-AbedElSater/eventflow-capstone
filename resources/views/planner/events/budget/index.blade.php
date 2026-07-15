@extends('layouts.planner')

@section('title', 'Budget - ' . $event->name)

@section('content')
@php
    $hasDraft = !is_null($event->aiBudgetDraft);
    $hasBudget = !is_null($event->budget);
@endphp

<style>
    .hub {
        --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --green-dark: #2C3821;
        font-family: 'Segoe UI', Manrope, sans-serif;
        min-height: 60vh;
        padding: 48px 24px;
        border-radius: 20px;
        background: var(--cream);
        color: var(--vampire);
    }
    .hub.dark {
        background: #241417;
        color: #fffaf2;
    }
    .hub.dark .card {
         background: var(--cream);
    }
    .hub.dark .card h2 { color: var(--vampire) }
    .hub.dark .card p { color:var(--green) }
    .hub.dark .sub { color: rgba(255, 250, 240, 0.75); }
    .hub.dark .eventname { color: rgba(255, 250, 240, 0.6); }

    .wrap { max-width: 1180px; margin: 0 auto; }

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }
    .eventname{
        font-size: 17px;
        font-weight: 700;
        color:var(--green);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin: 0 0 8px;
    }
    h1 {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 40px;
        color: var(--vampire);
        margin: 0 0 10px;
    }
    .hub.dark h1 { color: #fffaf2; }
    .sub {
        font-size: 17px;
        color: var(--green);
        line-height: 1.5;
        margin: 0;

    }
    .toggle {
        border: 0;
        border-radius: 20px;
        padding: 9px 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--vampire);
        color: #fff;
        white-space: nowrap;
        flex-shrink: 0;
        font-size: 1rem;
    }
    .hub.dark .toggle {
        background: var(--cream);
        color: var(--green);
    }

    .cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .card {
        background: var(--white);
        border-radius: 14px;
        padding: 40px 37px;
        display: flex;
        flex-direction: column;
        min-height: 320px;
    }
     .card.dark{
        background:var(--white);
    }
    .card h2 {
        font-size: 30px;
        color: var(--vampire);
        margin: 0 0 10px;
    }
    .card p {
        font-size: 15px;
        color: var(--green);
        line-height: 1.45;
        margin: 0 0 18px;
        min-height: 40px;
    }
    .status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 30px;
    }
    .status.ok { color: var(--green-dark); }
    .status.ok::before { background: var(--green); }
    .status.wait { color: var(--vampire); }
    .status.wait::before { background: var(--berry); }
    .status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .btn {
        display: block;
        text-align: center;
        text-decoration: none;
        font-weight: 700;
        font-size: 16px;
        padding: 11px 0;
        border-radius: 20px;
        color: #fff;
    }
    .btn-ai { background: var(--coral); }
    .btn-editor { background: var(--green); }
    .btn-disabled { background: #d8d1c6; color: #7b7368; pointer-events: none; }

    @media (max-width: 560px) {
        .cards { grid-template-columns: 1fr; }
    }
</style>

<div class="hub" id="budget-hub">
    <div class="wrap">
        <div class="topbar">
            <div>
                <p class="eventname">{{ $event->eventType->name ?? 'Event' }} · {{ $event->name }}</p>
                <h1>Budget</h1>
                <p class="sub">Generate AI budget suggestions, then turn them into an editable, finalized budget.</p>
            </div>
            <button type="button" class="toggle" id="mode-toggle">
                <i class="fas fa-circle-half-stroke"></i>
                Toggle Dark / Light
            </button>
        </div>

        <div class="cards">
            <div class="card">
                <h2>AI Forecast Studio</h2>
                <p>Generate budget suggestions by category and review totals.</p>
                <div class="status {{ $hasDraft ? 'ok' : 'wait' }}">
                    {{ $hasDraft ? 'Draft exists' : 'No draft yet' }}
                </div>
                <a href="{{ route('planner.events.budget.draft', $event) }}" class="btn btn-ai">
                    Open Forecast Studio
                </a>
            </div>

            <div class="card">
                <h2>Execution Console</h2>
                <p>Edit costs, update status, and finalize the budget.</p>
                <div class="status {{ $hasBudget ? 'ok' : 'wait' }}">
                    {{ $hasBudget ? 'Editor ready' : 'Editor locked' }}
                </div>
                <a href="{{ $hasBudget ? route('planner.events.budget.editor', $event) : '#' }}"
                   class="btn {{ $hasBudget ? 'btn-editor' : 'btn-disabled' }}">
                    {{ $hasBudget ? 'Open Execution Console' : 'Import draft first' }}
                </a>
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

        setMode(localStorage.getItem(key) || 'light');

        toggle.addEventListener('click', function () {
            const next = hub.classList.contains('dark') ? 'light' : 'dark';
            setMode(next);
        });
    })();
</script>
@endsection
