@extends('layouts.planner')

@section('title', 'Budget - ' . $event->name)

@section('content')
<div x-data="budgetGenerator({{ $event->id }})" class="budget-page" x-init="init()">

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
        .budget-page .mini-stat {
            border: none;
            box-shadow: 0 6px 20px rgba(98, 6, 7, 0.12);
        }
        .budget-header-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 28px;
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
    </style>

    <div class="command-header">
        <div class="command-title">
            <h1>{{ $event->name }}</h1>
            <p>{{ $event->eventType->name ?? 'Event' }} · {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }} · {{ $event->guest_estimate ?? 0 }} guests</p>
        </div>
        <div class="command-stats">
            <div class="mini-stat">
                <span class="number">{{ $event->guest_estimate ?? 0 }}</span>
                <span class="label">Guests</span>
            </div>
            <div class="mini-stat">
                <span class="number">{{ (int) now()->diffInDays(\Carbon\Carbon::parse($event->start_date), false) }}</span>
                <span class="label">Days Left</span>
            </div>
            <div class="mini-stat">
                <span class="number" x-text="aiResponse ? 'Ready' : '—'"></span>
                <span class="label">Budget Plan</span>
            </div>
        </div>
    </div>

    <div class="budget-header-actions">
        <button @click="generateBudget()" :disabled="loading" class="btn-primary">
            <span x-show="!loading"><i class="fas fa-robot"></i> Generate Budget Plan</span>
            <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> Generating...</span>
        </button>
    </div>

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
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection