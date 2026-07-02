@extends('layouts.assistant')

@section('content')
<div class="settings-shell">
    <div class="settings-grid">

        <section class="settings-card settings-card-header">
            <span class="settings-chip">Assistant Settings</span>
            <h1>Assistant Settings</h1>

            <p>
                Manage your notifications, account security, and preferences
                from one elegant dashboard designed to help you stay focused
                while assisting clients and planners.
            </p>
        </section>

        <section class="settings-card settings-card-panel settings-card-notification">

            <div class="card-row">

                <div>
                    <span class="panel-tag">Notifications</span>
                    <h2>In-app alerts</h2>
                </div>

                <label class="toggle-wrap">

                    <input
                        type="checkbox"
                        id="in_app_notifications"
                        {{ (isset($preferences) && isset($preferences->in_app_notifications))
                            ? ($preferences->in_app_notifications ? 'checked' : '')
                            : 'checked' }}>

                    <span class="toggle-slider"></span>

                </label>

            </div>

            <p class="panel-copy">
                Enable or disable assistant notifications instantly.
                Changes are saved automatically and the switch returns
                to its previous state if saving fails.
            </p>

        </section>

        <section class="settings-card settings-card-panel settings-card-actions">

            <div class="card-row">

                <div>
                    <span class="panel-tag">Account</span>
                    <h2>Account actions</h2>
                </div>

            </div>

            <div class="account-actions">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="button button-secondary">
                        Logout
                    </button>

                </form>

                <button
                    id="deleteAccountBtn"
                    class="button button-danger">
                    Delete Account
                </button>

            </div>

            <p class="panel-copy">
                Sign out securely or permanently delete your assistant
                account after confirmation.
            </p>

        </section>

    </div>
</div>

<script>

(function(){

const csrf=document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||'';

const toggle=document.getElementById('in_app_notifications');

let previousValue=toggle.checked;

function toast(message,error=false){

const toast=document.createElement('div');

toast.className='toast '+(error?'toast-error':'toast-success');

toast.textContent=message;

document.body.appendChild(toast);

setTimeout(()=>toast.remove(),3200);

}

async function savePreference(value){

try{

const response=await fetch('{{ route("assistant.settings.notifications.update") }}',{

method:'POST',

headers:{
'Content-Type':'application/json',
'Accept':'application/json',
'X-CSRF-TOKEN':csrf
},

body:JSON.stringify({
in_app_notifications:value
})

});

let data={};

try{
data=await response.json();
}catch(e){}

if(!response.ok){

throw new Error(data.message||'Unable to save preferences.');

}

previousValue=value;

toast(data.message||'Preferences saved successfully.');

}catch(error){

toggle.checked=previousValue;

toast(error.message||'Save failed.',true);

}

}

toggle.addEventListener('change',function(){

savePreference(this.checked);

});

document.getElementById('deleteAccountBtn').addEventListener('click',async()=>{

if(!confirm('Are you sure you want to permanently delete your account?')){

return;

}

const password=prompt('Enter your password to continue:');

if(!password){

return;

}

try{

const response=await fetch('{{ route("assistant.settings.delete") }}',{

method:'POST',

headers:{
'Content-Type':'application/json',
'Accept':'application/json',
'X-CSRF-TOKEN':csrf
},

body:JSON.stringify({

confirmation:true,

password:password

})

});

const data=await response.json();

if(!response.ok){

throw new Error(data.message||'Delete failed.');

}

window.location='{{ url("/") }}';

}catch(error){

toast(error.message||'Delete failed.',true);

}

});

})();

</script>

<style>
     :root{
    --bg:#F8F4EE;
    --surface:rgba(255,255,255,.96);
    --border:rgba(98,6,7,.14);
    --text:#1C2230;
    --muted:#5F6A72;
    --accent:#C63E4E;
    --accent-soft:rgba(198,62,78,.12);
    --success:#4B6F42;
    --danger:#620607;
}

.settings-shell{
    max-width:1140px;
    margin:0 auto;
    padding:48px 30px 80px;
    font-family:'Inter',system-ui,sans-serif;
    color:var(--text);
    background:var(--bg);
}

.settings-grid{
    display:grid;
    gap:24px;
}

.settings-card{
    border-radius:32px;
    background:var(--surface);
    border:1px solid var(--border);
    box-shadow:0 26px 78px rgba(98,6,7,.08);
}

.settings-card-header{
    padding:38px 34px;
    background:linear-gradient(180deg,
        rgba(255,250,245,.96),
        rgba(238,224,214,.96));
}

.settings-chip{
    display:inline-flex;
    padding:10px 16px;
    border-radius:999px;
    background:var(--accent-soft);
    color:var(--accent);
    font-weight:800;
    letter-spacing:.12em;
    text-transform:uppercase;
    font-size:.8rem;
}

.settings-card-header h1{
    margin:22px 0 14px;
    font-size:2.8rem;
    line-height:1.05;
}

.settings-card-header p{
    margin:0;
    color:var(--muted);
    line-height:1.9;
    max-width:720px;
}

.settings-card-panel{
    padding:32px;
    display:grid;
    gap:20px;
}

.card-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
}

.panel-tag{
    display:inline-flex;
    padding:10px 14px;
    border-radius:999px;
    background:rgba(91,112,81,.1);
    color:#3B5A40;
    font-weight:700;
    font-size:.82rem;
    text-transform:uppercase;
}

.settings-card-panel h2{
    margin:12px 0 0;
    font-size:1.7rem;
}

.panel-copy{
    margin:0;
    color:var(--muted);
    line-height:1.85;
}

.toggle-wrap{
    position:relative;
    width:122px;
    height:56px;
}

.toggle-wrap input{
    opacity:0;
    width:0;
    height:0;
}

.toggle-slider{
    position:absolute;
    inset:0;
    border-radius:999px;
    background:linear-gradient(
        90deg,
        rgba(91,112,81,.08),
        rgba(198,62,78,.08)
    );
    box-shadow:inset 0 10px 30px rgba(98,6,7,.06);
    transition:background .3s ease;
}

.toggle-slider::before{
    content:'';
    position:absolute;
    top:10px;
    left:10px;
    width:36px;
    height:36px;
    border-radius:50%;
    background:#fff;
    box-shadow:0 16px 40px rgba(28,34,48,.12);
    transition:transform .3s ease;
}

.toggle-wrap input:checked + .toggle-slider{
    background:linear-gradient(
        135deg,
        var(--accent),
        #E19184
    );
}

.toggle-wrap input:checked + .toggle-slider::before{
    transform:translateX(56px);
}

.account-actions{
    display:grid;
    gap:16px;
    margin-top:18px;
}

.button{
    width:100%;
    border:none;
    border-radius:18px;
    padding:16px 18px;
    cursor:pointer;
    font-weight:700;
    font-size:1rem;
    transition:
        transform .2s ease,
        box-shadow .2s ease,
        background .2s ease;
}

.button:hover{
    transform:translateY(-1px);
}

.button-primary{
    background:linear-gradient(
        135deg,
        var(--accent),
        #E19184
    );
    color:#fff;
    box-shadow:0 18px 46px rgba(198,62,78,.18);
}

.button-secondary{
    background:#fff;
    color:var(--text);
    border:1px solid rgba(98,6,7,.12);
    box-shadow:0 18px 46px rgba(98,6,7,.08);
}

.button-danger{
    background:linear-gradient(
        135deg,
        var(--danger),
        #A22430
    );
    color:#fff;
    box-shadow:0 18px 46px rgba(98,6,7,.18);
}
.toast{
    position:fixed;
    right:24px;
    bottom:24px;
    padding:14px 18px;
    border-radius:16px;
    color:#fff;
    box-shadow:0 18px 40px rgba(15,23,42,.16);
    z-index:9999;
    animation:fadeIn .25s ease;
}

.toast-success{
    background:linear-gradient(135deg,var(--success),#2D4F32);
}

.toast-error{
    background:linear-gradient(135deg,var(--accent),var(--danger));
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(12px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.settings-card{
    transition:
        transform .25s ease,
        box-shadow .25s ease;
}

.settings-card:hover{
    transform:translateY(-3px);
    box-shadow:0 34px 90px rgba(98,6,7,.10);
}

.button-primary:hover{
    box-shadow:0 22px 54px rgba(198,62,78,.22);
}

.button-danger:hover{
    box-shadow:0 22px 54px rgba(98,6,7,.22);
}

.button-secondary:hover{
    background:#fafafa;
}

.button:active{
    transform:scale(.98);
}

.settings-card-header,
.settings-card-panel{
    overflow:hidden;
}

.settings-card-panel::after{
    content:"";
    display:block;
    width:100%;
    height:1px;
    margin-top:4px;
    background:linear-gradient(
        90deg,
        transparent,
        rgba(198,62,78,.08),
        transparent
    );
}

@media (max-width:980px){

    .settings-shell{
        padding:36px 20px 60px;
    }

    .settings-grid{
        display:grid;
        gap:20px;
    }

    .settings-card-header h1{
        font-size:2.3rem;
    }

    .card-row{
        flex-direction:column;
        align-items:flex-start;
    }

    .toggle-wrap{
        margin-top:10px;
    }

    .button{
        font-size:.95rem;
    }

}

@media (max-width:600px){

    .settings-shell{
        padding:28px 16px 50px;
    }

    .settings-card{
        border-radius:24px;
    }

    .settings-card-header{
        padding:28px 24px;
    }

    .settings-card-panel{
        padding:24px;
    }

    .settings-card-header h1{
        font-size:2rem;
    }

    .settings-chip,
    .panel-tag{
        font-size:.72rem;
    }

    .toggle-wrap{
        width:110px;
        height:52px;
    }

    .toggle-slider::before{
        width:32px;
        height:32px;
        top:10px;
        left:10px;
    }

    .toggle-wrap input:checked + .toggle-slider::before{
        transform:translateX(48px);
    }

}
</style>
@endsection