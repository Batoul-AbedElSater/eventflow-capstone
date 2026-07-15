@extends('layouts.client')

@section('content')
<div class="settings-shell">
    <div class="settings-grid">

        <section class="settings-card settings-card-panel settings-card-notification">
            <div class="card-row">
                <div>
                    <span class="panel-tag">Notifications</span>
                    <h2>In-app alerts</h2>
                </div>
                <label class="toggle-wrap">
                    <input type="checkbox" id="in_app_notifications" {{ (isset($preferences) && isset($preferences->in_app_notifications)) ? ($preferences->in_app_notifications ? 'checked' : '') : 'checked' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <p class="panel-copy">Enable or disable client notifications instantly. The system saves your choice and rolls back the switch if the update fails.</p>
        </section>

        <section class="settings-card settings-card-panel settings-card-actions">
            <div class="card-row">
                <div>
                    <span class="panel-tag">Account</span>
                    <h2>Logout &amp; delete</h2>
                </div>
            </div>
            <div class="account-actions">
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="button button-secondary">Logout</button></form>
                <button id="deleteAccountBtn" class="button button-danger">Delete account</button>
            </div>
            <p class="panel-copy">Sign out securely or permanently delete your client
                account after confirmation.</p>
        </section>
    </div>
</div>

<script>
    (function(){
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const toggle = document.getElementById('in_app_notifications');
        let previousValue = toggle.checked;

        function toast(msg, isError = false){
            const el = document.createElement('div');
            el.className = 'toast ' + (isError ? 'toast-error' : 'toast-success');
            el.textContent = msg;
            document.body.appendChild(el);
            setTimeout(()=> el.remove(), 3200);
        }

        async function savePref(value){
            try{
                const res = await fetch('{{ route("client.settings.notifications.update") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ in_app_notifications: value })
                });
                let data = {};
                try{ data = await res.json(); }catch(e){}
                if(!res.ok) throw new Error(data.message || 'Unable to save preference');
                previousValue = value;
                toast(data.message || 'Preferences saved');
            }catch(err){
                toggle.checked = previousValue;
                toast(err.message || 'Save failed', true);
            }
        }

        toggle.addEventListener('change', function(){ savePref(this.checked); });

        document.getElementById('deleteAccountBtn').addEventListener('click', async ()=>{
            if(!confirm('Delete your account permanently?')) return;
            const password = prompt('Enter your password to confirm:');
            if(!password) return;
            try{
                const res = await fetch('{{ route("client.settings.delete") }}', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ confirmation: true, password })
                });
                const data = await res.json();
                if(!res.ok) throw new Error(data.message || 'Delete failed');
                location.href = '{{ url("/") }}';
            }catch(err){ toast(err.message || 'Delete failed', true); }
        });
    })();
</script>

<style>
    :root{
        --bg: #F8F4EE;
        --surface: rgba(255,255,255,.96);
        --border: rgba(98,6,7,.14);
        --text: #1C2230;
        --muted: #5F6A72;
        --accent: #C63E4E;
        --accent-soft: rgba(198,62,78,.12);
        --success: #4B6F42;
        --danger: #620607;

         --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --green-dark: #2C3821;
    }

    .settings-shell{
        max-width: 1140px;
        margin: 0 auto;
        padding: 48px 30px 80px;
        font-family: 'Inter', system-ui, sans-serif;
        color: var(--text);
        background: var(--cream);
    }

    .settings-grid{
        display: grid;
        gap: 24px;
    }

    .settings-card{
        border-radius: 32px;
        background: var(--white);
        border: 1px solid var(--border);
        box-shadow: 0 26px 78px rgba(98,6,7,.08);
    }

    .settings-card-header p{
        margin: 0;
        color: var(--muted);
        line-height: 1.9;
        max-width: 720px;
    }

    .settings-card-panel{
        padding: 32px;
        display: grid;
        gap: 20px;
    }

    .card-row{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .panel-tag{
        display: inline-flex;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(91,112,81,.1);
        color: #3B5A40;
        font-weight: 700;
        font-size: .82rem;
        text-transform: uppercase;
    }

    .settings-card-panel h2{
        margin: 12px 0 0;
        font-size: 1.7rem;
        color:var(--vampire);
    }

    .panel-copy{
        margin: 0;
        color: var(--green);
        line-height: 1.85;
    }

    .toggle-wrap{
        position: relative;
        width: 122px;
        height: 56px;
    }

    .toggle-wrap input{
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider{
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(91,112,81,.08), rgba(198,62,78,.08));
        box-shadow: inset 0 10px 30px rgba(98,6,7,.06);
        transition: background .3s ease;
    }

    .toggle-slider::before{
        content: '';
        position: absolute;
        top: 10px;
        left: 10px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 16px 40px rgba(28,34,48,.12);
        transition: transform .3s ease;
    }

    .toggle-wrap input:checked + .toggle-slider{
        background: linear-gradient(135deg, var(--accent), #E19184);
    }

    .toggle-wrap input:checked + .toggle-slider::before{
        transform: translateX(56px);
    }

    .account-actions{
        display: grid;
        gap: 16px;
        margin-top: 18px;
    }

    .button{
        width: 100%;
        border: none;
        border-radius: 18px;
        padding: 16px 18px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1rem;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .button:hover{
        transform: translateY(-1px);
    }

    .button-secondary{
        background: #fff;
        color: var(--text);
        border: 1px solid rgba(98,6,7,.12);
        box-shadow: 0 18px 46px rgba(98,6,7,.08);
    }

    .button-danger{
        background: var(--vampire);
        color: #fff;

    }

    .toast{
        position: fixed;
        right: 24px;
        bottom: 24px;
        padding: 14px 18px;
        border-radius: 16px;
        color: #fff;
        box-shadow: 0 18px 40px rgba(15,23,42,.16);
        z-index: 9999;
    }

    .toast-success{ background: linear-gradient(135deg, var(--success), #2D4F32); }
    .toast-error{ background: linear-gradient(135deg, var(--accent), var(--danger)); }

    @media (max-width: 980px) {
        .settings-grid{ display: grid; }
    }
</style>
@endsection