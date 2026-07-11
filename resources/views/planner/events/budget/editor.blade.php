@extends('layouts.planner')

@section('title', 'Budget Editor World - ' . $event->name)

@section('content')
@php
    $budget = $event->budget;
    $items = $budget->items;
    $totalClientBudget = (float) ($budget->total_client_budget ?? 0);
    $estimatedTotal = (float) ($budget->estimated_total ?? 0);
    $actualTotal = (float) ($budget->actual_total ?? 0);
    $assistantFeesTotal = (float) ($budget->total_assistant_fees ?? 0);
    $remainingEstimated = $totalClientBudget - $estimatedTotal;
    $remainingActual = $totalClientBudget - $actualTotal;
    $chartCategoryLabels = $items->pluck('category')->values()->all();
    $chartCategoryCosts = $items->pluck('estimated_cost')->map(function ($value) {
        return (float) $value;
    })->values()->all();
    $chartStatusCounts = [
        $items->where('status', 'pending')->count(),
        $items->where('status', 'confirmed')->count(),
        $items->where('status', 'paid')->count(),
    ];
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;600;700;800&display=swap');
    .editor-shell {
        --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --green-dark: #2C3821;
        --ink-soft: #5f4944;
        --ring: 0 0 0 3px rgba(198, 62, 78, 0.18);
        font-family: 'Manrope', sans-serif;
        min-height: 85vh;
        border-radius: 24px;
        padding: 26px;
        background:
            radial-gradient(circle at 5% 10%, rgba(225, 145, 132, 0.34) 0%, rgba(225, 145, 132, 0) 40%),
            radial-gradient(circle at 85% 8%, rgba(71, 91, 53, 0.24) 0%, rgba(71, 91, 53, 0) 36%),
            linear-gradient(145deg, #fff9f3 0%, var(--cream) 52%, #f6f0e6 100%);
        border: 1px solid rgba(98, 6, 7, 0.12);
    }
    .flow-top {
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .flow-nav { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .flow-node {
        background: var(--white);
        border: 1px solid rgba(98, 6, 7, 0.12);
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 800;
        color: var(--ink-soft);
    }
    .flow-node.active {
        background: linear-gradient(135deg, var(--berry), var(--vampire));
        color: var(--white);
        border-color: transparent;
        box-shadow: 0 10px 20px rgba(98, 6, 7, 0.22);
    }
    .flow-link { text-decoration: none; }
    .mode-toggle {
        border: 1px solid rgba(98, 6, 7, 0.2);
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: var(--amnesiac);
    }
    .editor-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 32px; }
    .editor-head h1 {
        margin: 0;
        font-family: 'Fraunces', serif;
        font-size: 38px;
        color: var(--vampire);
        line-height: 1.05;
    }
    .editor-head p { margin: 7px 0 0; color: var(--ink-soft); font-weight: 600; }
    .editor-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .ebtn {
        border: 0;
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }
    .ebtn:hover { transform: translateY(-1px); box-shadow: 0 12px 22px rgba(98, 6, 7, 0.16); filter: saturate(1.08); }
    .ebtn-mid { background: linear-gradient(135deg, var(--green), var(--green-dark)); color: var(--white); }
    .ebtn-soft { background: var(--white); color: var(--vampire); border: 1px solid rgba(98, 6, 7, 0.18); }
    .ebtn-danger {
        background: linear-gradient(155deg, #ffd3cf, #ffc3bc);
        color: var(--vampire);
        border: 1px solid rgba(98, 6, 7, 0.16);
        min-width: 118px;
    }
    .ebtn-add { background: linear-gradient(135deg, var(--green), #617b48); color: var(--white); }
    .ebtn-save {
        background: linear-gradient(135deg, var(--berry), var(--vampire));
        color: var(--white);
        min-width: 118px;
    }
    .panel {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(98, 6, 7, 0.11);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 10px 28px rgba(98, 6, 7, 0.09);
        backdrop-filter: blur(3px);
    }
    .panel h3 { margin: 0 0 12px; color: var(--vampire); font-family: 'Fraunces', serif; letter-spacing: 0.02em; }
    .grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 12px; }
    .span-12 { grid-column: span 12; }
    .span-8 { grid-column: span 8; }
    .span-6 { grid-column: span 6; }
    .span-4 { grid-column: span 4; }
    .span-3 { grid-column: span 3; }
    .kpi {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(245, 249, 229, 0.75));
        border-top: 4px solid var(--berry);
        border-radius: 14px;
        padding: 12px;
        box-shadow: 0 8px 18px rgba(98, 6, 7, 0.08);
    }
    .kpi small { display: block; color: #7f6762; margin-bottom: 6px; font-weight: 800; text-transform: uppercase; font-size: 11px; }
    .kpi strong { font-size: 24px; color: var(--vampire); font-family: 'Fraunces', serif; }
    .kpi .meta { margin-top: 5px; font-size: 12px; font-weight: 700; }
    .meta-up { color: var(--green); }
    .meta-down { color: var(--berry); }
    .fields { display: grid; grid-template-columns: repeat(2, minmax(220px, 1fr)); gap: 12px; margin: 12px 0; }
    .in, .sel, .ta {
        width: 100%;
        border: 1px solid rgba(98, 6, 7, 0.18);
        border-radius: 10px;
        padding: 9px 10px;
        background: var(--white);
        color: #3f2a26;
        font: inherit;
    }
    .ta { min-height: 90px; resize: vertical; }
    .in:focus, .sel:focus, .ta:focus { outline: none; box-shadow: var(--ring); border-color: var(--berry); }
    .toolbar { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; margin-bottom: 10px; }
    .table-wrap {
        overflow: auto;
        border: 1px solid rgba(98, 6, 7, 0.14);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.86);
    }
    .table { width: 100%; border-collapse: collapse; min-width: 1020px; }
    .table th, .table td { padding: 10px; border-bottom: 1px solid rgba(98, 6, 7, 0.08); vertical-align: top; }
    .table th {
        position: sticky;
        top: 0;
        background: linear-gradient(180deg, #fff6f4, #f6eee3);
        color: var(--vampire);
        text-align: left;
    }
    .item-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 14px;
        padding: 12px;
        align-items: stretch;
        border: 1px solid rgba(98, 6, 7, 0.08);
        background: linear-gradient(120deg, rgba(255, 255, 255, 0.95), rgba(239, 231, 218, 0.58));
        border-radius: 12px;
    }
    .item-form { display: grid; grid-template-columns: 16% 16% 11% 12% 33% 12%; gap: 10px; }
    .actions-rail {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 124px;
    }
    .actions-rail .ebtn {
        width: 100%;
        min-height: 46px;
        border-radius: 16px;
    }
    .row-icon-btn {
        width: 52px !important;
        min-width: 52px;
        max-width: 52px;
        padding: 0;
        position: relative;
        box-shadow: 0 8px 16px rgba(98, 6, 7, 0.14);
    }
    .row-icon-btn i { font-size: 15px; }
    .row-icon-btn::after {
        content: '';
        position: absolute;
        inset: 6px;
        border-radius: 12px;
        border: 1px dashed rgba(255, 255, 255, 0.34);
        pointer-events: none;
    }
    .hidden-row { display: none !important; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; border-radius: 99px; padding: 5px 10px; font-size: 12px; font-weight: 700; }
    .st-draft { background: #fde8e6; color: var(--vampire); }
    .st-approved { background: #f7dce0; color: var(--berry); }
    .st-finalized { background: #efe7da; color: #82531f; }
    .st-shared { background: #eaf2dd; color: var(--green-dark); }
    .alert { padding: 10px 12px; border-radius: 10px; margin-bottom: 10px; font-weight: 700; }
    .ok { background: #e8f3da; border: 1px solid #bfd3a7; color: var(--green-dark); }
    .err { background: #ffe8e4; border: 1px solid #f4bbb3; color: var(--vampire); }
    .editor-shell.dark {
        background:
            radial-gradient(circle at 14% 16%, rgba(225, 145, 132, 0.22) 0%, rgba(225, 145, 132, 0) 38%),
            radial-gradient(circle at 82% 84%, rgba(71, 91, 53, 0.24) 0%, rgba(71, 91, 53, 0) 35%),
            linear-gradient(160deg, #2d1516 0%, #3b1e23 44%, #1f2917 100%);
        color: #f8f5f0;
        border-color: rgba(245, 249, 229, 0.15);
    }
    .editor-shell.dark .panel,
    .editor-shell.dark .kpi,
    .editor-shell.dark .table th,
    .editor-shell.dark .table-wrap,
    .editor-shell.dark .item-row {
        background: rgba(245, 249, 229, 0.07);
        border-color: rgba(245, 249, 229, 0.22);
        color: #f8f5f0;
    }
    .editor-shell.dark .editor-head h1,
    .editor-shell.dark .panel h3,
    .editor-shell.dark .kpi strong,
    .editor-shell.dark .table th,
    .editor-shell.dark .flow-node {
        color: #fff6e9;
    }
    .editor-shell.dark .flow-node {
        background: rgba(245, 249, 229, 0.08);
        border-color: rgba(245, 249, 229, 0.22);
    }
    .editor-shell.dark .flow-node.active { color: var(--white); }
    .editor-shell.dark .editor-head p,
    .editor-shell.dark .kpi small { color: rgba(255, 255, 255, 0.78); }
    .editor-shell.dark .in,
    .editor-shell.dark .sel,
    .editor-shell.dark .ta {
        background: rgba(245, 249, 229, 0.1);
        color: #fffdf7;
        border-color: rgba(245, 249, 229, 0.28);
    }
    .editor-shell.dark label,
    .editor-shell.dark small,
    .editor-shell.dark p,
    .editor-shell.dark .meta,
    .editor-shell.dark .status-badge {
        color: rgba(255, 250, 240, 0.92);
    }
    .editor-shell.dark .sel option {
        background: #2f1a1d;
        color: #fff9ef;
    }
    .editor-shell.dark .mode-toggle {
        background: linear-gradient(135deg, var(--amnesiac), #d9e6c0);
        color: var(--green-dark);
    }
    .editor-shell.dark .ebtn-soft {
        background: rgba(245, 249, 229, 0.12);
        color: #fff7ed;
        border-color: rgba(245, 249, 229, 0.28);
    }
    .editor-shell.dark .ebtn-danger {
        background: linear-gradient(145deg, #f0c5bf, #e8a39a);
        color: #491113;
    }
    @media (max-width: 1180px) {
        .span-8,.span-6,.span-4,.span-3 { grid-column: span 12; }
        .fields { grid-template-columns: 1fr; }
        .toolbar { grid-template-columns: 1fr; }
        .editor-head { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="editor-shell">
    <div class="flow-top">
        <div class="flow-nav">
            <a class="flow-link" href="{{ route('planner.events.budget', $event) }}"><span class="flow-node"><i class="fas fa-globe"></i> Universe Hub</span></a>
            <a class="flow-link" href="{{ route('planner.events.budget.draft', $event) }}"><span class="flow-node"><i class="fas fa-robot"></i> AI Studio</span></a>
            <span class="flow-node active"><i class="fas fa-table"></i> Editor World</span>
        </div>
        <button type="button" class="mode-toggle" id="mode-toggle">
            <i class="fas fa-circle-half-stroke"></i> Toggle Dark / Light
        </button>
    </div>

    <div class="editor-head">
        <div>
            <h1>Budget Editor World</h1>
            <p>Create your final operational budget with creative controls, advanced visuals, and precision editing.</p>
        </div>
        <div class="editor-actions">
            <a href="{{ route('planner.events.budget', $event) }}" class="ebtn ebtn-soft"><i class="fas fa-home"></i> Hub</a>
            <a href="{{ route('planner.events.budget.draft', $event) }}" class="ebtn ebtn-soft"><i class="fas fa-robot"></i> AI Studio</a>
            <a href="{{ route('planner.tasks.index') }}" class="ebtn ebtn-mid"><i class="fas fa-tasks"></i> Tasks Page</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert err">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert err">{{ $errors->first() }}</div>
    @endif

    <div class="grid">
        <div class="kpi span-3"><small>Total Client Budget</small><strong>${{ number_format($totalClientBudget, 2) }}</strong></div>
        <div class="kpi span-3"><small>Estimated Total</small><strong>${{ number_format($estimatedTotal, 2) }}</strong><div class="meta {{ $remainingEstimated >= 0 ? 'meta-up' : 'meta-down' }}">{{ $remainingEstimated >= 0 ? 'Under' : 'Over' }} by ${{ number_format(abs($remainingEstimated), 2) }}</div></div>
        <div class="kpi span-3"><small>Actual Total</small><strong>${{ number_format($actualTotal, 2) }}</strong><div class="meta {{ $remainingActual >= 0 ? 'meta-up' : 'meta-down' }}">{{ $remainingActual >= 0 ? 'Under' : 'Over' }} by ${{ number_format(abs($remainingActual), 2) }}</div></div>
        <div class="kpi span-3"><small>Status</small><strong style="font-size:18px;"><span class="status-badge st-{{ $budget->status }}"><i class="fas fa-circle"></i>{{ ucfirst($budget->status) }}</span></strong><div class="meta">Assistant Fees: ${{ number_format($assistantFeesTotal, 2) }}</div></div>
    </div>
    <br>
  

    <div class="grid">
        <div class="panel span-8">
            <h3>Planner Controls</h3>
            <form method="POST" action="{{ route('planner.events.budget.update', $event) }}" class="js-submit-lock">
                @csrf
                @method('PUT')
                <div class="fields">
                    <div>
                        <label>Status</label>
                        <select name="status" id="planner-budget-status" class="sel">
                            <option value="draft" @selected($budget->status === 'draft')>Draft</option>
                            <option value="approved" @selected($budget->status === 'approved')>Approved</option>
                            <option value="finalized" @selected($budget->status === 'finalized')>Finalized</option>
                            <option value="shared" @selected($budget->status === 'shared')>Shared</option>
                        </select>
                    </div>
                    <div>
                        <label>Total Client Budget</label>
                        <input type="number" step="0.01" min="0" name="total_client_budget" class="in" value="{{ $budget->total_client_budget }}">
                    </div>
                    <div>
                        <label>Planner Fee</label>
                        <input type="number" step="0.01" min="0" name="planner_fee" class="in" value="{{ $budget->planner_fee }}">
                    </div>
                    <div style="display:flex;align-items:flex-end;gap:8px;">
                        <input type="checkbox" id="shared_with_client" name="shared_with_client" value="1" @checked($budget->shared_with_client)>
                        <label for="shared_with_client">Shared with client</label>
                    </div>
                </div>
                <div style="margin-bottom:10px;">
                    <label>Planner Strategic Notes</label>
                    <textarea name="planner_notes" class="ta">{{ old('planner_notes', $budget->planner_notes) }}</textarea>
                </div>
                <button type="submit" class="ebtn ebtn-save" data-loading-text='<i class="fas fa-spinner fa-spin"></i> Saving...'><i class="fas fa-save"></i> Save Planner Controls</button>
            </form>
        </div>

        <div class="panel span-4">
            <h3>Budget Visuals</h3>
            <canvas id="budgetBarChart" height="180"></canvas>
            <hr style="border:0;border-top:1px solid #eee4e7;margin:12px 0;">
            <canvas id="statusPieChart" height="160"></canvas>
        </div>
    </div>

    <div class="panel">
        <h3>Advanced Budget Table</h3>
        <div class="toolbar">
            <input id="search-items" type="text" class="in" placeholder="Search category/title/notes...">
            <select id="filter-status" class="sel">
                <option value="all">All statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="paid">Paid</option>
            </select>
            <select id="sort-items" class="sel">
                <option value="default">Default order</option>
                <option value="estimated_desc">Highest estimated first</option>
                <option value="estimated_asc">Lowest estimated first</option>
            </select>
            <button type="button" id="toggle-expensive" class="ebtn ebtn-soft">High-cost only</button>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 14%;">Category</th>
                        <th style="width: 14%;">Title</th>
                        <th style="width: 10%;">Estimated</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 25%;">Notes</th>
                        <th style="width: 25%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="editor-items-body">
                    @foreach($items as $index => $item)
                        <tr
                            class="editor-item-wrap"
                            data-status="{{ $item->status }}"
                            data-estimated="{{ (float) $item->estimated_cost }}"
                            data-search="{{ mb_strtolower($item->category . ' ' . ($item->title ?? '') . ' ' . ($item->notes ?? '')) }}"
                        >
                            <td colspan="6" style="padding:0;border-bottom:0;">
                                <div class="item-row">
                                    <form method="POST" action="{{ route('planner.events.budget.items.update', [$event, $item]) }}" class="item-form js-submit-lock" id="item-form-{{ $item->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input name="category" class="in" value="{{ $item->category }}" required>
                                        <input name="title" class="in" value="{{ $item->title }}">
                                        <input name="estimated_cost" type="number" step="0.01" min="0" class="in" value="{{ $item->estimated_cost }}" required>
                                        <select name="status" class="sel js-item-status" required>
                                            <option value="pending" @selected($item->status === 'pending')>Pending</option>
                                            <option value="confirmed" @selected($item->status === 'confirmed')>Confirmed</option>
                                            <option value="paid" @selected($item->status === 'paid')>Paid</option>
                                        </select>
                                        <textarea name="notes" class="ta">{{ $item->notes }}</textarea>
                                        <div class="actions-rail">
                                            <button class="ebtn ebtn-save row-save-btn row-icon-btn" type="submit" data-loading-text='<i class="fas fa-spinner fa-spin"></i>' title="Save item" aria-label="Save item">
                                                <i class="fas fa-floppy-disk"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <div class="actions-rail">
                                        <form method="POST" action="{{ route('planner.events.budget.items.destroy', [$event, $item]) }}" class="js-submit-lock" onsubmit="return confirm('Delete this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ebtn ebtn-danger row-icon-btn" data-loading-text='<i class="fas fa-spinner fa-spin"></i>' title="Remove item" aria-label="Remove item"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <h3>Add New Budget Item</h3>
        <form method="POST" action="{{ route('planner.events.budget.items.store', $event) }}" class="js-submit-lock" style="display:grid;grid-template-columns:repeat(5,minmax(160px,1fr));gap:10px;">
            @csrf
            <input name="category" class="in" placeholder="Category" required value="{{ old('category') }}">
            <input name="title" class="in" placeholder="Title" value="{{ old('title') }}">
            <input name="estimated_cost" type="number" step="0.01" min="0" class="in" placeholder="Estimated Cost" required value="{{ old('estimated_cost') }}">
            <select name="status" class="sel" required>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="paid">Paid</option>
            </select>
            <input name="description" class="in" placeholder="Description (what this item includes)" value="{{ old('description') }}">
            <textarea name="notes" class="ta" style="grid-column:1/-1;" placeholder="Planner notes, vendor assumptions, or decisions">{{ old('notes') }}</textarea>
            <div>
                <button type="submit" class="ebtn ebtn-add" data-loading-text='<i class="fas fa-spinner fa-spin"></i> Adding...'><i class="fas fa-plus-circle"></i> Add Budget Item</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
    (function () {
        const wrappers = Array.from(document.querySelectorAll('.editor-item-wrap'));
        const body = document.getElementById('editor-items-body');
        const searchEl = document.getElementById('search-items');
        const statusEl = document.getElementById('filter-status');
        const sortEl = document.getElementById('sort-items');
        const expensiveBtn = document.getElementById('toggle-expensive');
        const totalClientBudget = {{ $totalClientBudget }};
        let expensiveOnly = false;

        function lockButton(button) {
            if (!button || button.disabled) return;
            if (button.dataset.loadingText) {
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = button.dataset.loadingText;
            }
            button.disabled = true;
        }

        function applyFilters() {
            const query = (searchEl?.value || '').trim().toLowerCase();
            const status = statusEl?.value || 'all';
            wrappers.forEach(function (row) {
                const s = row.dataset.status || '';
                const estimated = Number(row.dataset.estimated || 0);
                const searchable = row.dataset.search || '';
                const okStatus = status === 'all' || s === status;
                const okSearch = query === '' || searchable.includes(query);
                const okExpensive = !expensiveOnly || (totalClientBudget > 0 && estimated >= totalClientBudget * 0.20);
                row.classList.toggle('hidden-row', !(okStatus && okSearch && okExpensive));
            });
        }

        function applySort() {
            const mode = sortEl?.value || 'default';
            const sorted = [...wrappers];
            if (mode === 'estimated_desc') {
                sorted.sort((a, b) => Number(b.dataset.estimated || 0) - Number(a.dataset.estimated || 0));
            } else if (mode === 'estimated_asc') {
                sorted.sort((a, b) => Number(a.dataset.estimated || 0) - Number(b.dataset.estimated || 0));
            }
            sorted.forEach(function (row) { body.appendChild(row); });
        }

        searchEl?.addEventListener('input', applyFilters);
        statusEl?.addEventListener('change', applyFilters);
        sortEl?.addEventListener('change', function () { applySort(); applyFilters(); });
        expensiveBtn?.addEventListener('click', function () {
            expensiveOnly = !expensiveOnly;
            expensiveBtn.textContent = expensiveOnly ? 'Showing high-cost only' : 'High-cost only';
            applyFilters();
        });

        document.querySelectorAll('.js-submit-lock').forEach(function (form) {
            form.addEventListener('submit', function () {
                lockButton(form.querySelector('button[type="submit"]'));
            });
        });

        const categoryLabels = @json($chartCategoryLabels);
        const categoryCosts = @json($chartCategoryCosts);
        const statusCounts = @json($chartStatusCounts);

        const barCanvas = document.getElementById('budgetBarChart');
        if (barCanvas && categoryLabels.length > 0) {
            new Chart(barCanvas, {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Estimated Cost',
                        data: categoryCosts,
                        backgroundColor: 'rgba(198, 62, 78, 0.82)',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        const pieCanvas = document.getElementById('statusPieChart');
        if (pieCanvas) {
            new Chart(pieCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Paid'],
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: ['#E19184', '#C63E4E', '#475B35']
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });
        }

        @if(session('success'))
            confetti({ particleCount: 120, spread: 80, origin: { y: 0.6 } });
        @endif

        const container = document.querySelector('.editor-shell');
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

        document.querySelectorAll('.js-item-status').forEach(function (statusSelect) {
            statusSelect.addEventListener('change', function () {
                if (this.form) {
                    const saveButton = this.form.querySelector('.row-save-btn');
                    this.form.requestSubmit(saveButton || undefined);
                }
            });
        });

        const plannerStatusEl = document.getElementById('planner-budget-status');
        const sharedWithClientEl = document.getElementById('shared_with_client');
        if (plannerStatusEl && sharedWithClientEl) {
            plannerStatusEl.addEventListener('change', function () {
                if (this.value === 'shared') {
                    sharedWithClientEl.checked = true;
                }
            });
        }

        applySort();
        applyFilters();
    })();
</script>
@endsection
