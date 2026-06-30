@extends('planner.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Team Management</h2>
        <p class="section-subtitle">Manage your assistants and team members</p>
    </div>

    <!-- Add Team Member -->
    <div class="settings-card add-member-card" style="border-top: 4px solid var(--coral-haze);">
        <h3 class="card-title">➕ Add Team Member</h3>
        <form id="addTeamForm" onsubmit="addTeamMember(event)">
            <div class="form-row">
                <div class="form-group">
                    <label>Assistant Email</label>
                    <input type="email" name="email" placeholder="Search assistant by email" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required style="width: 100%; padding: 12px 16px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Raleway', sans-serif;">
                        <option value="assistant">Assistant</option>
                        <option value="vendor_manager">Vendor Manager</option>
                        <option value="coordinator">Coordinator</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                Add Member
            </button>
        </form>
    </div>

    <!-- Team Members List -->
    <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
        <h3 class="card-title">👥 Your Team</h3>
        
        @if($assistants && count($assistants) > 0)
            <div class="team-members-list">
                @foreach($assistants as $assistant)
                    <div class="team-member-item">
                        <div class="member-avatar">
                            <img src="{{ $assistant->getAvatarUrlAttribute() }}" alt="{{ $assistant->name }}">
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">{{ $assistant->name }}</h4>
                            <p class="member-email">{{ $assistant->email }}</p>
                            <p class="member-role">
                                <span class="role-badge" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    {{ ucfirst($assistant->role) }}
                                </span>
                            </p>
                        </div>
                        <div class="member-actions">
                            <button type="button" class="btn-action" onclick="editMember({{ $assistant->id }})" title="Edit">
                                ✎
                            </button>
                            <button type="button" class="btn-action remove" onclick="removeMember({{ $assistant->id }})" title="Remove">
                                🗑️
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: #999; text-align: center; padding: 40px 0;">No team members yet. Add your first assistant above!</p>
        @endif
    </div>

    <!-- Permissions Management -->
    <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
        <h3 class="card-title">🔑 Permission Settings</h3>
        <div class="permissions-grid">
            <div class="permission-item">
                <input type="checkbox" id="perm-view-events" checked>
                <label for="perm-view-events">
                    <strong>View Events</strong>
                    <p>Team can view all your events</p>
                </label>
            </div>
            <div class="permission-item">
                <input type="checkbox" id="perm-manage-vendors" checked>
                <label for="perm-manage-vendors">
                    <strong>Manage Vendors</strong>
                    <p>Team can add and manage vendors</p>
                </label>
            </div>
            <div class="permission-item">
                <input type="checkbox" id="perm-manage-tasks" checked>
                <label for="perm-manage-tasks">
                    <strong>Manage Tasks</strong>
                    <p>Team can create and assign tasks</p>
                </label>
            </div>
            <div class="permission-item">
                <input type="checkbox" id="perm-client-contact" checked>
                <label for="perm-client-contact">
                    <strong>Contact Clients</strong>
                    <p>Team can communicate with clients</p>
                </label>
            </div>
            <div class="permission-item">
                <input type="checkbox" id="perm-manage-budget">
                <label for="perm-manage-budget">
                    <strong>Manage Budget</strong>
                    <p>Team can modify event budgets</p>
                </label>
            </div>
            <div class="permission-item">
                <input type="checkbox" id="perm-generate-reports">
                <label for="perm-generate-reports">
                    <strong>Generate Reports</strong>
                    <p>Team can create event reports</p>
                </label>
            </div>
        </div>
    </div>

    <!-- Team Insights (FIXED) -->
    <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
        <h3 class="card-title">📊 Team Performance</h3>
        <div class="insights-grid">
            <div class="insight-item">
                <p class="insight-label">Total Tasks Assigned</p>
                <p class="insight-value" style="color: #E19184;">
                    {{ $assistants->sum(function($a) { 
                        return $a->assignedTasks()->count(); 
                    }) }}
                </p>
            </div>
            <div class="insight-item">
                <p class="insight-label">Completed This Month</p>
                <p class="insight-value" style="color: #C63E4E;">
                    {{ $assistants->sum(function($a) { 
                        return $a->assignedTasks()
                            ->where('tasks.status', 'done')
                            ->whereMonth('tasks.updated_at', now()->month)
                            ->count(); 
                    }) }}
                </p>
            </div>
            <div class="insight-item">
                <p class="insight-label">Average Rating</p>
                <p class="insight-value" style="color: #475B35;">⭐ 4.8</p>
            </div>
            <div class="insight-item">
                <p class="insight-label">Team Members</p>
                <p class="insight-value" style="color: #620607;">{{ $assistants->count() }}</p>
            </div>
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
        margin-bottom: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.1);
    }

    .team-members-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .team-member-item {
        display: flex;
        gap: 16px;
        padding: 16px;
        background: white;
        border-radius: 8px;
        align-items: center;
    }

    .member-avatar img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--coral-haze);
    }

    .member-info {
        flex: 1;
    }

    .member-name {
        font-weight: 700;
        color: #333;
        margin: 0 0 4px 0;
    }

    .member-email {
        font-size: 13px;
        color: #999;
        margin: 0 0 8px 0;
    }

    .member-role {
        margin: 0;
    }

    .role-badge {
        display: inline-block;
    }

    .member-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f0f0f0;
    }

    .btn-action:hover {
        background: var(--coral-haze);
        color: white;
    }

    .btn-action.remove:hover {
        background: #ff6b6b;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .permission-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: white;
        border-radius: 8px;
    }

    .permission-item input {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #e0e0e0;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 2px;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .permission-item input:checked {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        border-color: var(--calypso-berry);
    }

    .permission-item label {
        cursor: pointer;
    }

    .permission-item label strong {
        display: block;
        color: #333;
        margin-bottom: 2px;
    }

    .permission-item label p {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .insight-item {
        background: white;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }

    .insight-label {
        font-size: 12px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .insight-value {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 900;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .permissions-grid {
            grid-template-columns: 1fr;
        }

        .insights-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<script>
    function addTeamMember(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('addTeamForm'));
        
        fetch('{{ route("planner.settings.team.add") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    }

    function removeMember(memberId) {
        if (confirm('Are you sure you want to remove this team member?')) {
            // Add remove member logic
        }
    }

    function editMember(memberId) {
        // Add edit member logic
    }
</script>
@endsection