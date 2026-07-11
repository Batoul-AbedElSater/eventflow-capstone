@extends('layouts.planner')

@section('title', 'AI Draft Studio - ' . $event->name)

@section('content')
@php
    $draft = $event->aiBudgetDraft;
    $ai = $draft?->ai_response ?? [];
    $budget = $event->budget;
    $budgetItems = $budget?->items ?? collect();
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;600&display=swap');
    .draft-shell {
        --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --green-dark: #2C3821;
        font-family: 'Inter', sans-serif;
        min-height: 84vh;
        border-radius: 20px;
        padding: 24px;
        background: linear-gradient(140deg, #fff8f1 0%, var(--cream) 42%, #f2f5e6 100%);
    }
    .flow-top {
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .flow-nav { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .flow-node {
        background: #fff;
        border: 1px solid #e1d7da;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
        color: #6a5e62;
    }
    .flow-node.active { background: linear-gradient(135deg, var(--berry), var(--vampire)); color: #fff; border-color: transparent; box-shadow: 0 6px 14px rgba(98, 6, 7, 0.25); }
    .flow-link { text-decoration: none; }
    .mode-toggle {
        border: 0;
        border-radius: 10px;
        padding: 9px 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: #fff;
    }
    .draft-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
    .draft-head h1 { margin: 0; font-family: 'Outfit', sans-serif; color: var(--vampire); font-size: 34px; }
    .draft-head p { margin: 6px 0 0; color: #675a5d; }
    .draft-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .draft-btn {
        border: 0; border-radius: 10px; padding: 10px 14px; text-decoration: none;
        font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
    }
    .btn-generate { background: linear-gradient(135deg, var(--berry), var(--vampire)); color: #fff; }
    .btn-import { background: linear-gradient(135deg, var(--coral), var(--berry)); color: #fff; }
    .btn-editor { background: linear-gradient(135deg, var(--green), var(--green-dark)); color: #fff; }
    .btn-back { background: #f8f2e8; color: var(--vampire); border: 1px solid rgba(98, 6, 7, 0.15); }
    .draft-panel {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #ede4e6;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 8px 24px rgba(79, 31, 42, 0.07);
        animation: rise 0.4s ease;
    }
    @keyframes rise { from { transform: translateY(8px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    .stat { background: #fffaf6; border-radius: 10px; padding: 12px; border-top: 3px solid var(--berry); }
    .stat small { color: #7b6f72; display: block; margin-bottom: 5px; }
    .stat strong { font-size: 24px; color: #301519; }
    .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px; }
    .cat {
        border: 1px solid #eee3e5;
        border-radius: 10px;
        padding: 12px;
        background: linear-gradient(180deg, #fff, #fefafb);
    }
    .cat h4 { margin: 0 0 8px; color: #4f1f2a; display: flex; justify-content: space-between; font-family: 'Outfit', sans-serif; }
    .list { margin: 0; padding-left: 18px; color: #5e5356; font-size: 13px; }
    .alert { padding: 10px 12px; border-radius: 10px; margin-bottom: 10px; font-weight: 600; }
    .ok { background: #ebf7ef; border: 1px solid #b9ddc2; color: #25673a; }
    .err { background: #fff1f0; border: 1px solid #f1c2bf; color: #8c2c25; }
    .loading {
        display: none; text-align: center; padding: 26px; color: #4f1f2a; font-weight: 700;
    }
    .context-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }
    .context-box {
        border-radius: 10px;
        background: #fff6f0;
        border: 1px solid rgba(198, 62, 78, 0.2);
        padding: 10px;
    }
    .context-box small { display:block; color:#6e5752; margin-bottom:4px; }
    .context-box strong { color:var(--vampire); font-size:18px; }
    .draft-shell.dark {
        background: linear-gradient(140deg, #1b0b0f 0%, #2b1016 45%, #1b271a 100%);
        color: #fff9f0;
    }
    .draft-shell.dark .draft-panel,
    .draft-shell.dark .context-box,
    .draft-shell.dark .stat,
    .draft-shell.dark .cat {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.16);
        color: #fff9f0;
    }
    .draft-shell.dark h1,
    .draft-shell.dark h2,
    .draft-shell.dark h3,
    .draft-shell.dark h4,
    .draft-shell.dark p,
    .draft-shell.dark small,
    .draft-shell.dark li,
    .draft-shell.dark .cat h4,
    .draft-shell.dark .context-box small,
    .draft-shell.dark .context-box strong,
    .draft-shell.dark .stat strong,
    .draft-shell.dark .stat small {
        color: #fffaf2 !important;
    }
    .draft-shell.dark select,
    .draft-shell.dark input,
    .draft-shell.dark textarea {
        background: rgba(245, 249, 229, 0.1);
        color: #fffaf2;
        border-color: rgba(245, 249, 229, 0.28);
    }
    .draft-shell.dark select option {
        background: #2f1a1d;
        color: #fff9ef;
    }
    .draft-shell.dark .mode-toggle { background: var(--amnesiac); color: var(--green-dark); }
    @media (max-width: 1020px) { .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>

<div class="draft-shell">
    <div class="flow-top">
        <div class="flow-nav">
            <a class="flow-link" href="{{ route('planner.events.budget', $event) }}"><span class="flow-node"><i class="fas fa-globe"></i> Universe Hub</span></a>
            <span class="flow-node active"><i class="fas fa-robot"></i> AI Studio</span>
            <a class="flow-link" href="{{ $event->budget ? route('planner.events.budget.editor', $event) : '#' }}"><span class="flow-node"><i class="fas fa-table"></i> Editor World</span></a>
        </div>
        <button type="button" class="mode-toggle" id="mode-toggle">
            <i class="fas fa-circle-half-stroke"></i> Toggle Dark / Light
        </button>
    </div>

    <div class="draft-head">
        <div>
            <h1>AI Draft Studio</h1>
            <p>Generate AI suggestions that now use your latest planner edits from the Budget Editor.</p>
        </div>
        <div class="draft-actions">
            <a href="{{ route('planner.events.budget', $event) }}" class="draft-btn btn-back"><i class="fas fa-home"></i> Workspace Hub</a>
            <button id="generate-ai-budget" type="button" class="draft-btn btn-generate" data-loading-text='<i class="fas fa-spinner fa-spin"></i> Generating...'>
                <i class="fas fa-robot"></i> Generate AI Plan
            </button>
            @if($draft && !$event->budget)
                <form method="POST" action="{{ route('planner.events.budget.import', $event) }}" class="js-submit-lock" onsubmit="return confirm('Import this AI draft to Budget Editor?');">
                    @csrf
                    <button class="draft-btn btn-import" type="submit" data-loading-text='<i class="fas fa-spinner fa-spin"></i> Importing...'>
                        <i class="fas fa-file-import"></i> Import to Editor
                    </button>
                </form>
            @elseif($event->budget)
                <a href="{{ route('planner.events.budget.editor', $event) }}" class="draft-btn btn-editor"><i class="fas fa-arrow-right"></i> Go to Editor</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert err">{{ session('error') }}</div>
    @endif

    <div id="draft-loading" class="draft-panel loading">
        <i class="fas fa-sparkles"></i> AI is designing your budget draft...
    </div>

    @if($budget)
        <div class="draft-panel">
                <h3 style="margin:0 0 10px;color:#620607;font-family:'Outfit',sans-serif;">Planner Budget Context Used by AI</h3>
            <p style="margin:0 0 10px;color:#5c5f70;font-size:13px;">
                When you click Generate AI Plan, AI considers these current editor values.
            </p>
            <div class="context-grid">
                <div class="context-box"><small>Editor Status</small><strong>{{ ucfirst($budget->status) }}</strong></div>
                <div class="context-box"><small>AI Draft Status</small><strong>{{ ucfirst($draft->status ?? 'draft') }}</strong></div>
                <div class="context-box"><small>Client Budget</small><strong>${{ number_format((float) ($budget->total_client_budget ?? 0), 2) }}</strong></div>
                <div class="context-box"><small>Estimated Total</small><strong>${{ number_format((float) ($budget->estimated_total ?? 0), 2) }}</strong></div>
                <div class="context-box"><small>Line Items</small><strong>{{ $budgetItems->count() }}</strong></div>
            </div>
        </div>
    @endif

    @if($draft)
        <div class="draft-panel">
            <h3 style="margin:0 0 12px;color:#4f1f2a;font-family:'Outfit',sans-serif;">AI Budget Overview</h3>
            <div class="stat-grid">
                <div class="stat"><small>Total Client Budget</small><strong>${{ number_format((float) ($ai['total_client_budget'] ?? 0), 2) }}</strong></div>
                <div class="stat"><small>Planner Fee</small><strong>${{ number_format((float) ($ai['planner_fee_amount'] ?? 0), 2) }}</strong></div>
                <div class="stat"><small>Assistant Fees</small><strong>${{ number_format((float) ($ai['total_assistant_fees'] ?? 0), 2) }}</strong></div>
                <div class="stat"><small>Remaining</small><strong>${{ number_format((float) ($ai['remaining_for_event'] ?? 0), 2) }}</strong></div>
            </div>
        </div>

        <div class="draft-panel">
            <h3 style="margin:0 0 12px;color:#4f1f2a;font-family:'Outfit',sans-serif;">Suggested Categories</h3>
            <div class="cat-grid">
                @foreach(($ai['categories'] ?? []) as $category)
                    <div class="cat">
                        <h4>
                            <span>{{ $category['category'] ?? 'General' }}</span>
                            <span>${{ number_format((float) ($category['estimated_cost'] ?? 0), 2) }}</span>
                        </h4>
                        @if(!empty($category['suggested_orders']))
                            <ul class="list">
                                @foreach($category['suggested_orders'] as $order)
                                    <li>{{ $order }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        @if(!empty($ai['warnings']))
            <div class="draft-panel">
                <h3 style="margin:0 0 10px;color:#8c2c25;font-family:'Outfit',sans-serif;">Warnings</h3>
                <ul class="list">
                    @foreach($ai['warnings'] as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($ai['planner_questions']))
            <div class="draft-panel">
                <h3 style="margin:0 0 10px;color:#1e4e88;font-family:'Outfit',sans-serif;">Planner Questions</h3>
                <ul class="list">
                    @foreach($ai['planner_questions'] as $question)
                        <li>{{ $question }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @else
        <div class="draft-panel">
            <h3 style="margin:0 0 8px;color:#4f1f2a;font-family:'Outfit',sans-serif;">No draft yet</h3>
            <p style="margin:0;color:#66595c;">Click <strong>Generate AI Plan</strong> to start Part A suggestions, then import to Budget Editor (Part B).</p>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
    (function () {
        const generateBtn = document.getElementById('generate-ai-budget');
        const loadingPanel = document.getElementById('draft-loading');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        function launchConfetti() {
            confetti({ particleCount: 130, spread: 90, origin: { y: 0.6 } });
        }

        function lockButton(button) {
            if (!button || button.disabled) return;
            if (button.dataset.loadingText) {
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = button.dataset.loadingText;
            }
            button.disabled = true;
        }

        if (generateBtn) {
            generateBtn.addEventListener('click', async function () {
                lockButton(generateBtn);
                if (loadingPanel) loadingPanel.style.display = 'block';
                try {
                    const response = await fetch('{{ route('planner.events.budget.generate', $event) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Failed to generate draft.');
                    }
                    launchConfetti();
                    setTimeout(function () {
                        window.location.reload();
                    }, 550);
                } catch (error) {
                    alert(error.message || 'Failed to generate draft.');
                    window.location.reload();
                }
            });
        }

        document.querySelectorAll('.js-submit-lock').forEach(function (form) {
            form.addEventListener('submit', function () {
                lockButton(form.querySelector('button[type="submit"]'));
            });
        });

        @if(session('success'))
            launchConfetti();
        @endif

        const container = document.querySelector('.draft-shell');
        const modeToggle = document.getElementById('mode-toggle');
        const key = 'budget_mode';
        if (container && modeToggle) {
            function applyMode(mode) {
                container.classList.toggle('dark', mode === 'dark');
                localStorage.setItem(key, mode);
            }

            applyMode(localStorage.getItem(key) || 'light');
            modeToggle.addEventListener('click', function () {
                const next = container.classList.contains('dark') ? 'light' : 'dark';
                applyMode(next);
            });
        }
    })();
</script>
@endsection
