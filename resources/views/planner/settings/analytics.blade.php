@extends('planner.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Business Analytics</h2>
        <p class="section-subtitle">Track your business performance and growth</p>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card" style="border-top: 4px solid var(--coral-haze);">
            <p class="metric-label">Events This Month</p>
            <p class="metric-value" style="color: var(--coral-haze);">{{ $analytics['total_events_this_month'] ?? 0 }}</p>
            <p class="metric-change">↑ 12% from last month</p>
        </div>

        <div class="metric-card" style="border-top: 4px solid var(--calypso-berry);">
            <p class="metric-label">Client Satisfaction</p>
            <p class="metric-value" style="color: var(--calypso-berry);">{{ $analytics['client_satisfaction'] ?? 0 }}%</p>
            <p class="metric-change">↑ Excellent</p>
        </div>

        <div class="metric-card" style="border-top: 4px solid var(--garden-green);">
            <p class="metric-label">Repeat Client Rate</p>
            <p class="metric-value" style="color: var(--garden-green);">{{ $analytics['repeat_client_rate'] ?? 0 }}%</p>
            <p class="metric-change">↑ Growing</p>
        </div>

        <div class="metric-card" style="border-top: 4px solid var(--vampire-hunter);">
            <p class="metric-label">Avg Event Budget</p>
            <p class="metric-value" style="color: var(--vampire-hunter);">${{ number_format($analytics['average_event_budget'] ?? 0, 0) }}</p>
            <p class="metric-change">↑ 8% higher</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">📊 Events by Month</h3>
            <div class="chart-placeholder">
                <p style="color: #999; text-align: center; padding: 60px 20px;">Events Chart (Jan-Dec)</p>
            </div>
        </div>

        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">💰 Revenue Tracking</h3>
            <div class="chart-placeholder">
                <p style="color: #999; text-align: center; padding: 60px 20px;">Revenue Chart</p>
            </div>
        </div>
    </div>

    <!-- Performance Breakdown -->
    <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
        <h3 class="card-title">🎯 Performance Breakdown</h3>
        <div class="breakdown-list">
            <div class="breakdown-item">
                <div class="breakdown-label">Wedding Events</div>
                <div class="breakdown-bar">
                    <div class="breakdown-fill" style="width: 65%; background: linear-gradient(90deg, #E19184 0%, #C63E4E 100%);"></div>
                </div>
                <div class="breakdown-stat">65% (13 events) • $125,000</div>
            </div>

            <div class="breakdown-item">
                <div class="breakdown-label">Corporate Events</div>
                <div class="breakdown-bar">
                    <div class="breakdown-fill" style="width: 20%; background: linear-gradient(90deg, #475B35 0%, #C63E4E 100%);"></div>
                </div>
                <div class="breakdown-stat">20% (4 events) • $45,000</div>
            </div>

            <div class="breakdown-item">
                <div class="breakdown-label">Birthday & Celebrations</div>
                <div class="breakdown-bar">
                    <div class="breakdown-fill" style="width: 15%; background: linear-gradient(90deg, #F5F9E5 0%, #E19184 100%);"></div>
                </div>
                <div class="breakdown-stat">15% (3 events) • $18,000</div>
            </div>
        </div>
    </div>

    <!-- Team Performance -->
    <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
        <h3 class="card-title">👥 Team Performance</h3>
        <div class="team-performance-list">
            <div class="team-perf-item">
                <div class="team-info">
                    <h4>Sarah Assistant</h4>
                    <p class="team-role">Decoration & Setup</p>
                </div>
                <div class="team-stats">
                    <span class="stat">15 tasks completed</span>
                    <span class="stat">⭐ 4.9 rating</span>
                </div>
            </div>

            <div class="team-perf-item">
                <div class="team-info">
                    <h4>Ahmed Coordinator</h4>
                    <p class="team-role">Event Coordination</p>
                </div>
                <div class="team-stats">
                    <span class="stat">12 tasks completed</span>
                    <span class="stat">⭐ 4.7 rating</span>
                </div>
            </div>

            <div class="team-perf-item">
                <div class="team-info">
                    <h4>Fatima Catering Liaison</h4>
                    <p class="team-role">Catering Management</p>
                </div>
                <div class="team-stats">
                    <span class="stat">8 tasks completed</span>
                    <span class="stat">⭐ 4.8 rating</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Data -->
    <div class="settings-card" style="border-top: 4px solid #2196F3;">
        <h3 class="card-title">📥 Export Analytics</h3>
        <p class="card-description">Download your analytics and reports</p>
        <div class="export-buttons">
            <button type="button" class="export-btn" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                📊 Export as PDF
            </button>
            <button type="button" class="export-btn" style="background: linear-gradient(135deg, var(--garden-green) 0%, var(--coral-haze) 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                📈 Export as Excel
            </button>
        </div>
    </div>
</div>

<style>
    .settings-section {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .section-header {
        margin-bottom: 24px;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .section-subtitle {
        color: #999;
        font-size: 14px;
    }

    /* Metrics Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(71, 91, 53, 0.08);
        transition: all 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(71, 91, 53, 0.12);
    }

    .metric-label {
        font-size: 12px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .metric-value {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .metric-change {
        font-size: 12px;
        color: #666;
        margin: 0;
    }

    /* Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .settings-card {
        background: linear-gradient(135deg, rgba(245,249,229,0.5) 0%, rgba(239,231,218,0.5) 100%);
        border-radius: 12px;
        padding: 32px;
        transition: all 0.3s ease;
    }

    .settings-card:hover {
        box-shadow: 0 8px 30px rgba(71, 91, 53, 0.1);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 16px;
    }

    .card-description {
        font-size: 13px;
        color: #999;
        margin-bottom: 16px;
    }

    .chart-placeholder {
        min-height: 200px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Breakdown List */
    .breakdown-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .breakdown-item {
        background: white;
        border-radius: 8px;
        padding: 16px;
    }

    .breakdown-label {
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .breakdown-bar {
        width: 100%;
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .breakdown-fill {
        height: 100%;
        border-radius: 4px;
    }

    .breakdown-stat {
        font-size: 12px;
        color: #999;
    }

    /* Team Performance */
    .team-performance-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .team-perf-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: white;
        border-radius: 8px;
    }

    .team-info h4 {
        font-weight: 700;
        color: #333;
        margin: 0 0 4px 0;
    }

    .team-role {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    .team-stats {
        display: flex;
        gap: 16px;
    }

    .stat {
        font-size: 12px;
        color: #666;
        font-weight: 600;
    }

    /* Export Buttons -->
    .export-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .export-btn {
        flex: 1;
        min-width: 160px;
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }

        .team-perf-item {
            flex-direction: column;
            gap: 12px;
        }

        .export-buttons {
            flex-direction: column;
        }

        .export-btn {
            min-width: auto;
            width: 100%;
        }
    }
</style>
@endsection