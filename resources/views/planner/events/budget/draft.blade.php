@extends('layouts.planner')

@section('title', 'Budget - ' . $event->name)

@section('content')
@php
    $budget = $event->budget;
    $draft = $event->aiBudgetDraft;
    $budgetItems = $budget?->items ?? collect();
@endphp
<div x-data="budgetGenerator({{ $event->id }})" class="budget-page" x-init="init()" id="budget-page-root">

    <style>
        .budget-page {
            --cream: #F5EBDF;
            --cream-deep: #EFE7DA;
            --card: #FFFFFF;
            --maroon: #5C2430;
            --olive: #475B35;
            --coral: #E19184;
            --coral-deep: #C63E4E;
            --muted: #8B7B72;
            --amber-bg: #FBEAE6;
            --amber-border: #E8B9AC;
            --amber-text: #8C3A2E;
            --blue-bg: #EFF6FF;
            --blue-border: #BFDBFE;
            --blue-text: #1E3A8A;
            --red-bg: #FEE2E2;
            --red-border: #FECACA;
            --red-text: #991B1B;
            --green: #15803D;
            --shadow: 0 8px 28px rgba(92, 36, 48, 0.08);
            --shadow-sm: 0 2px 10px rgba(92, 36, 48, 0.06);
            font-family: inherit;
        }
        .budget-error {
            background: var(--red-bg);
            border: 1px solid var(--red-border);
            color: var(--red-text);
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .state-panel {
            text-align: center;
            padding: 90px 20px;
            background: var(--card);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
        }
        .state-icon {
            font-size: 56px;
            color: var(--coral);
        }
        .state-icon.spin { opacity: 0.9; }
        .state-icon.dim { opacity: 0.35; }
        .state-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 24px;
            color: var(--maroon);
            margin: 20px 0 8px 0;
        }
        .state-copy {
            color: var(--muted);
            font-size: 15px;
            max-width: 420px;
            margin: 0 auto;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }
        .stat-tile {
            background: var(--card);
            border-radius: 18px;
            padding: 22px 24px;
            box-shadow: var(--shadow-sm);
            border-top: 4px solid var(--tile-accent, var(--coral));
        }
        .stat-label {
            font-size: 12px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 10px;
        }
        .stat-value {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 30px;
            font-weight: 700;
            color: var(--tile-accent, var(--maroon));
        }
        .panel {
            background: var(--card);
            border-radius: 18px;
            padding: 26px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 28px;
        }
        .panel-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--maroon);
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .panel-title i { color: var(--coral); }
        .panel-title .count { font-size: 15px; color: var(--olive); font-family: inherit; }
        .assistant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 14px;
        }
        .assistant-card {
            background: var(--amber-bg);
            border: 1px solid var(--amber-border);
            border-radius: 10px;
            padding: 18px;
        }
        .assistant-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .assistant-name { color: var(--amber-text); font-size: 15px; font-weight: 700; }
        .assistant-fee {
            background: white;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 700;
            color: var(--green);
            font-size: 14px;
        }
        .assistant-list { list-style: none; padding: 0; margin: 0; }
        .assistant-list li {
            font-size: 13px;
            color: var(--muted);
            padding: 4px 0;
            display: flex;
            align-items: flex-start;
        }
        .assistant-list i { color: var(--coral); margin-right: 9px; margin-top: 3px; font-size: 12px; }
        .assistant-note {
            font-size: 12px;
            color: var(--amber-text);
            margin: 16px 0 0 0;
            font-style: italic;
            text-align: center;
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 18px;
        }
        .category-card {
            background: var(--card);
            border-radius: 10px;
            padding: 22px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--coral);
        }
        .category-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
        }
        .category-name { font-size: 16px; font-weight: 700; color: var(--maroon); margin: 0; }
        .category-cost { font-size: 20px; font-weight: 700; color: var(--green); font-family: Georgia, serif; }
        .category-note {
            background: var(--blue-bg);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            color: var(--blue-text);
            margin-bottom: 14px;
        }
        .category-block { font-size: 13px; color: var(--muted); margin-bottom: 14px; }
        .category-block strong { color: var(--olive); }
        .category-block div { padding: 3px 0; }
        .category-work {
            font-size: 12px;
            color: var(--blue-text);
            border-top: 1px solid var(--cream-deep);
            padding-top: 12px;
        }
        .category-work div { padding: 2px 0; }
        .notice {
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 20px;
        }
        .notice-warning { background: var(--red-bg); border: 1px solid var(--red-border); }
        .notice-question { background: var(--blue-bg); border: 1px solid var(--blue-border); }
        .notice-title {
            margin: 0 0 10px 0;
            font-size: 15px;
            font-weight: 700;
        }
        .notice-warning .notice-title { color: var(--red-text); }
        .notice-question .notice-title { color: var(--blue-text); }
        .notice-warning div.notice-line { color: var(--red-text); }
        .notice-question div.notice-line { color: var(--blue-text); }
        .notice-line { font-size: 14px; padding: 3px 0; }
        @media (max-width: 900px) {
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 560px) {
            .stat-grid { grid-template-columns: 1fr; }
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .top-nav { display: flex; gap: 10px; flex-wrap: wrap; }
        .top-right-group { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .top-btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card);
            color: var(--maroon);
            box-shadow: var(--shadow-sm);
        }
        .mode-toggle {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--maroon);
            color: #fff;
            font-size: 1rem;
        }

        .context-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }
        .context-box {
            border-radius: 10px;
            background: var(--amber-bg);
            border: 1px solid var(--amber-border);
            padding: 10px;
        }
        .context-box small { display:block; color:#6e5752; margin-bottom:4px; }
        .context-box strong { color:var(--maroon); font-size:18px; }
        @media (max-width: 900px) {
            .context-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        /* ================================
           DARK MODE
           Mirrors the Budget Editor's dark theme: every card, panel, tile,
           and text element gets explicit dark-safe colors instead of only
           swapping the page background.
        ================================ */
        .budget-page.dark {
            background:
                radial-gradient(circle at 14% 16%, rgba(225, 145, 132, 0.26) 0%, rgba(225, 145, 132, 0) 42%),
                radial-gradient(circle at 82% 84%, rgba(71, 91, 53, 0.26) 0%, rgba(71, 91, 53, 0) 40%),
                linear-gradient(160deg, #5a3a3e 0%, #684450 44%, #4a5439 100%);
            border-radius: 20px;
            padding: 24px;
            color: #fffdfa;
        }

        .budget-page.dark .top-btn {
            background: rgba(245, 249, 229, 0.22);
            color: #fffaf0;
            box-shadow: none;
            border: 1px solid rgba(245, 249, 229, 0.36);
        }
        .budget-page.dark .mode-toggle {
            background: var(--cream);
            color: var(--maroon);
        }
        .budget-page.dark .btn-primary {
            filter: saturate(1.1);
        }

        .budget-page.dark .panel,
        .budget-page.dark .stat-tile,
        .budget-page.dark .category-card,
        .budget-page.dark .state-panel {
            background: rgba(245, 249, 229, 0.2);
            border-color: rgba(245, 249, 229, 0.34);
            box-shadow: none;
            color: #fffdfa;
        }
        .budget-page.dark .category-card { border: 1px solid rgba(225, 145, 132, 0.55); }

        .budget-page.dark .panel-title,
        .budget-page.dark .state-title,
        .budget-page.dark .stat-value,
        .budget-page.dark .category-name,
        .budget-page.dark .context-box strong {
            color: #fffdfa;
        }
        .budget-page.dark .stat-value { color: var(--tile-accent, #fffdfa); }

        .budget-page.dark .stat-label,
        .budget-page.dark .state-copy,
        .budget-page.dark .category-block,
        .budget-page.dark .assistant-list li,
        .budget-page.dark .context-box small {
            color: rgba(255, 253, 250, 0.95);
        }
        .budget-page.dark .category-block strong { color: #e7f2d7; }
        .budget-page.dark .panel-title .count { color: #e7f2d7; }

        .budget-page.dark .assistant-card {
            background: rgba(225, 145, 132, 0.24);
            border-color: rgba(225, 145, 132, 0.5);
        }
        .budget-page.dark .assistant-name,
        .budget-page.dark .assistant-note {
            color: #ffe6de;
        }
        .budget-page.dark .assistant-fee {
            background: rgba(245, 249, 229, 0.3);
            color: #d9f4c4;
        }

        .budget-page.dark .category-note {
            background: rgba(59, 130, 246, 0.26);
            color: #e6efff;
        }
        .budget-page.dark .category-work {
            color: #e6efff;
            border-top-color: rgba(245, 249, 229, 0.28);
        }

        .budget-page.dark .context-box {
            background: rgba(225, 145, 132, 0.24);
            border-color: rgba(225, 145, 132, 0.48);
        }

        .budget-page.dark .notice-warning {
            background: rgba(153, 27, 27, 0.32);
            border-color: rgba(252, 165, 165, 0.55);
        }
        .budget-page.dark .notice-question {
            background: rgba(30, 58, 138, 0.4);
            border-color: rgba(191, 219, 254, 0.55);
        }
        .budget-page.dark .notice-warning .notice-title,
        .budget-page.dark .notice-warning div.notice-line {
            color: #ffdbd5;
        }
        .budget-page.dark .notice-question .notice-title,
        .budget-page.dark .notice-question div.notice-line {
            color: #e6efff;
        }

        .budget-page.dark .budget-error {
            background: rgba(153, 27, 27, 0.32);
            border-color: rgba(252, 165, 165, 0.55);
            color: #ffdbd5;
        }
    </style>

    <div class="top-actions">
        <div class="top-nav">
            <a href="{{ route('planner.events.budget', $event) }}" class="top-btn"><i class="fas fa-house"></i> Hub</a>
                   @if($event->budget)
                <a href="{{ route('planner.events.budget.editor', $event) }}" class="top-btn"><i class="fas fa-table"></i> Budget Editor</a>
            @elseif($draft)
                <form action="{{ route('planner.events.budget.import', $event) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="top-btn" style="cursor:pointer;">
                        <i class="fas fa-file-import"></i> Import & Open Editor
                    </button>
                </form>
                        @else
                <span class="top-btn" style="opacity:0.5;pointer-events:none;"><i class="fas fa-table"></i> Budget Editor</span>
            @endif
            <a href="{{ route('planner.tasks.index') }}" class="top-btn"><i class="fas fa-tasks"></i> Tasks Page</a>
        </div>
             <div class="top-right-group">
            <button @click="generateBudget()" :disabled="loading" class="btn-primary">
                <span x-show="!loading"><i class="fas fa-robot"></i> Generate Budget Plan</span>
                <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> Generating...</span>
            </button>
            @if($draft && !$budget)
                <form action="{{ route('planner.events.budget.import', $event) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #475B35, #2C3821);">
                        <i class="fas fa-file-import"></i> Import to Budget Editor
                    </button>
                </form>
            @endif
            <button type="button" class="mode-toggle" id="mode-toggle">
                <i class="fas fa-circle-half-stroke"></i> Toggle Dark / Light
            </button>
        </div>
    </div>

    @if($budget)
        <div class="panel">
            <h3 class="panel-title" style="color:#620607;">Planner Budget Context Used by AI</h3>
            <p style="margin:0 0 14px;color:#5c5f70;font-size:13px;">
                When you click Generate Budget Plan, AI considers these current editor values.
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

    <div x-show="error" x-cloak class="budget-error" x-text="error"></div>

    <div x-show="loading" class="state-panel">
        <i class="fas fa-spinner fa-spin state-icon spin"></i>
        <p class="state-copy" style="margin-top: 20px;">AI is analyzing your event and creating a budget plan...</p>
    </div>

    <div x-show="aiResponse && !loading" x-cloak>
        <div class="stat-grid">
            <div class="stat-tile" style="--tile-accent: var(--maroon);">
                <div class="stat-label">Total Client Budget</div>
                <div class="stat-value" x-text="'$' + (aiResponse.total_client_budget || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></div>
            </div>
            <div class="stat-tile" style="--tile-accent: #1E40AF;">
                <div class="stat-label">Planner Fee (15%)</div>
                <div class="stat-value" x-text="'$' + (aiResponse.planner_fee_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></div>
            </div>
            <div class="stat-tile" style="--tile-accent: var(--amber-text);">
                <div class="stat-label">Assistant Fees</div>
                <div class="stat-value" x-text="'$' + (aiResponse.total_assistant_fees || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></div>
            </div>
            <div class="stat-tile" style="--tile-accent: var(--green);">
                <div class="stat-label">Remaining for Event</div>
                <div class="stat-value" x-text="'$' + (aiResponse.remaining_for_event || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></div>
            </div>
        </div>

        <div class="panel">
            <h3 class="panel-title"><i class="fas fa-users"></i> Suggested Assistants: <span class="count" x-text="aiResponse.suggested_assistants"></span></h3>
            <div class="assistant-grid">
                <template x-for="a in aiResponse.assistants" :key="a.assistant_number">
                    <div class="assistant-card">
                        <div class="assistant-card-head">
                            <span class="assistant-name">Assistant <span x-text="a.assistant_number"></span></span>
                            <span class="assistant-fee">$<span x-text="a.fee.toFixed(2)"></span></span>
                        </div>
                        <ul class="assistant-list">
                            <template x-for="r in a.responsibilities" :key="r">
                                <li><i class="fas fa-check-circle"></i><span x-text="r"></span></li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>
            <p class="assistant-note">⚠️ These are suggestions only. Assign tasks manually from the Tasks page.</p>
        </div>

        <div class="panel">
            <h3 class="panel-title"><i class="fas fa-list"></i> Budget Categories <span class="count">(Total: $<span x-text="(aiResponse.final_budget_for_categories || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>)</span></h3>
            <div class="category-grid">
                <template x-for="cat in aiResponse.categories" :key="cat.category">
                    <div class="category-card">
                        <div class="category-head">
                            <h4 class="category-name" x-text="cat.category"></h4>
                            <span class="category-cost" x-text="'$' + cat.estimated_cost.toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                        </div>
                        <div x-show="cat.guest_based_note" class="category-note" x-text="cat.guest_based_note"></div>
                        <div class="category-block">
                            <strong>Suggested Orders:</strong>
                            <template x-for="order in cat.suggested_orders" :key="order">
                                <div>• <span x-text="order"></span></div>
                            </template>
                        </div>
                        <div class="category-work">
                            <strong>Assistant Work:</strong>
                            <template x-for="work in cat.suggested_assistant_work" :key="work">
                                <div>• <span x-text="work"></span></div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="aiResponse.warnings?.length" class="notice notice-warning">
            <h4 class="notice-title">⚠️ Warnings</h4>
            <template x-for="w in aiResponse.warnings" :key="w">
                <div class="notice-line" x-text="'• ' + w"></div>
            </template>
        </div>

        <div x-show="aiResponse.planner_questions?.length" class="notice notice-question">
            <h4 class="notice-title">💬 Questions to Clarify with Client</h4>
            <template x-for="q in aiResponse.planner_questions" :key="q">
                <div class="notice-line" x-text="'❓ ' + q"></div>
            </template>
        </div>
    </div>

    <div x-show="!aiResponse && !loading" class="state-panel">
        <i class="fas fa-robot state-icon dim"></i>
        <h2 class="state-title">AI Budget Planning</h2>
        <p class="state-copy">Click "Generate Budget Plan" to get AI-powered budget suggestions for this event.</p>
    </div>

</div>

<script>
function budgetGenerator(eventId) {
    return {
        loading: false,
        error: null,
        aiResponse: null,

        async init() {
            // Auto-load existing draft on page load
            const saved = localStorage.getItem('budget_draft_' + eventId);
            if (saved) {
                try {
                    this.aiResponse = JSON.parse(saved);
                } catch (e) {
                    // Invalid saved data, ignore
                }
            }
        },

        async generateBudget() {
            this.loading = true;
            this.error = null;

            try {
                const response = await fetch(`/planner/events/${eventId}/budget/generate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    credentials: 'include',
                });

                const contentType = response.headers.get('content-type') || '';
                const isJson = contentType.includes('application/json');
                const data = isJson ? await response.json() : null;

                if (!response.ok) {
                    const serverMessage = data?.message
                        || (isJson ? null : await response.text())
                        || `Request failed with status ${response.status}`;
                    throw new Error(serverMessage);
                }

                if (!data || !data.success) {
                    throw new Error(data.message || 'Failed to generate budget');
                }

                this.aiResponse = data.data.ai_response;

                          // Save to localStorage so it persists on revisit
                localStorage.setItem('budget_draft_' + eventId, JSON.stringify(data.data.ai_response));

                // Reload page so Import button appears
                window.location.reload();

            } catch (err) {
                
                this.error = err.message || 'Something went wrong. Please try again.';
                console.error('Budget generation error:', err);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
<script>
    (function () {
        const key = 'budget_mode';
        const container = document.getElementById('budget-page-root');
        const toggle = document.getElementById('mode-toggle');
        if (!container || !toggle) return;

        function setMode(mode) {
            container.classList.toggle('dark', mode === 'dark');
            localStorage.setItem(key, mode);
        }

        setMode(localStorage.getItem(key) || 'light');

        toggle.addEventListener('click', function () {
            const next = container.classList.contains('dark') ? 'light' : 'dark';
            setMode(next);
        });
    })();
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
