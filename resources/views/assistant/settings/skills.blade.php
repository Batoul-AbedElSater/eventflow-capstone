@extends('assistant.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Skills & Expertise</h2>
        <p class="section-subtitle">Showcase your abilities and qualifications</p>
    </div>

    <form id="skillsForm" onsubmit="updateSkills(event)">
        <!-- Specializations -->
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">🎯 Specializations</h3>
            <p class="card-description">What services are you specialized in?</p>
            <div class="specializations-grid">
                @php
                    $specializations = $preferences->specializations ?? [];
                    $specs = ['decoration'=>'Decoration & Setup', 'catering'=>'Catering Coordination', 'photography'=>'Photography Support', 'entertainment'=>'Entertainment', 'coordination'=>'Event Coordination', 'florist'=>'Florist Work', 'makeup'=>'Makeup & Hair', 'henna'=>'Henna Artist'];
                @endphp
                @foreach($specs as $value => $label)
                    <label class="spec-checkbox">
                        <input type="checkbox" name="specializations[]" value="{{ $value }}" {{ in_array($value, $specializations) ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Experience Level -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">📈 Experience Level</h3>
            <div class="experience-selector">
                <label class="exp-option">
                    <input type="radio" name="experience_level" value="beginner" {{ ($preferences->experience_level ?? '') == 'beginner' ? 'checked' : '' }}>
                    <span class="exp-label"><strong>Beginner</strong><p>0-1 year of experience</p></span>
                </label>
                <label class="exp-option">
                    <input type="radio" name="experience_level" value="intermediate" {{ ($preferences->experience_level ?? '') == 'intermediate' ? 'checked' : '' }}>
                    <span class="exp-label"><strong>Intermediate</strong><p>1-3 years of experience</p></span>
                </label>
                <label class="exp-option">
                    <input type="radio" name="experience_level" value="expert" {{ ($preferences->experience_level ?? '') == 'expert' ? 'checked' : '' }}>
                    <span class="exp-label"><strong>Expert</strong><p>3+ years of experience</p></span>
                </label>
            </div>
        </div>

        <!-- Certifications -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">🏆 Certifications & Awards</h3>
            <p class="card-description">Add credentials that boost your credibility</p>
            <div class="certifications-list">
                @if($preferences->certifications)
                    @foreach($preferences->certifications as $cert)
                        <div class="cert-item">
                            <span>{{ $cert }}</span>
                            <button type="button" class="cert-remove" onclick="removeCert(this)">×</button>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="cert-input-group">
                <input type="text" id="certInput" class="cert-input" placeholder="e.g., Professional Event Planning Certificate">
                <button type="button" class="cert-add-btn" onclick="addCertification()">Add</button>
            </div>
        </div>

        <!-- Portfolio Link -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">🎨 Portfolio Link</h3>
            <div class="form-group">
                <label>Portfolio Website</label>
                <input type="url" name="portfolio_link" placeholder="https://myportfolio.com" value="{{ $preferences->portfolio_link ?? '' }}">
                <p class="form-hint">Link to your portfolio website or portfolio page</p>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
            Save Skills & Experience
        </button>
    </form>
</div>

<style>
    .settings-section { display: flex; flex-direction: column; gap: 32px; }
    .section-header { margin-bottom: 24px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 900; margin-bottom: 8px; }
    .section-subtitle { color: #999; font-size: 14px; }
    .settings-card {
        background: linear-gradient(135deg, rgba(245,249,229,0.5) 0%, rgba(239,231,218,0.5) 100%);
        border-radius: 12px;
        padding: 32px; transition: all 0.3s ease;
    }
    .settings-card:hover { box-shadow: 0 8px 30px rgba(71, 91, 53, 0.1); }
    .card-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 16px; }
    .card-description { font-size: 13px; color: #999; margin-bottom: 16px; }
    .specializations-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
    .spec-checkbox {
        display: flex; align-items: center; padding: 12px 16px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;
    }
    .spec-checkbox input { appearance: none; width: 20px; height: 20px; border: 2px solid #e0e0e0; border-radius: 4px; cursor: pointer; margin-right: 12px; transition: all 0.3s ease; }
    .spec-checkbox input:checked { background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); border-color: var(--calypso-berry); }
    .spec-checkbox span { font-weight: 600; color: #333; }
    .spec-checkbox:hover { border-color: var(--coral-haze); background: rgba(225,145,132,0.05); }
    .experience-selector { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
    .exp-option { position: relative; cursor: pointer; }
    .exp-option input { display: none; }
    .exp-label { display: block; padding: 16px; border: 2px solid #e0e0e0; border-radius: 8px; text-align: center; transition: all 0.3s ease; }
    .exp-label strong { display: block; color: #333; margin-bottom: 4px; }
    .exp-label p { font-size: 12px; color: #999; margin: 0; }
    .exp-option input:checked + .exp-label { border-color: var(--coral-haze); background: rgba(225,145,132,0.1); }
    .certifications-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .cert-item { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .cert-remove { background: none; border: none; color: white; cursor: pointer; font-size: 16px; line-height: 1; }
    .cert-input-group { display: flex; gap: 8px; }
    .cert-input { flex: 1; padding: 12px 16px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Raleway', sans-serif; font-size: 14px; }
    .cert-input:focus { outline: none; border-color: var(--coral-haze); box-shadow: 0 0 0 3px rgba(225,145,132,0.1); }
    .cert-add-btn { padding: 12px 24px; background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
    .form-group { margin-bottom: 0; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input { width: 100%; padding: 12px 16px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Raleway', sans-serif; font-size: 14px; transition: all 0.3s ease; }
    .form-group input:focus { outline: none; border-color: var(--coral-haze); box-shadow: 0 0 0 3px rgba(225,145,132,0.1); }
    .form-hint { font-size: 12px; color: #999; margin-top: 8px; }
    @media (max-width: 768px) { .specializations-grid { grid-template-columns: 1fr; } .experience-selector { grid-template-columns: 1fr; } }
</style>

<script>
    function updateSkills(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('skillsForm'));
        fetch('{{ route("assistant.settings.skills") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => { if (data.success) alert(data.message); });
    }

    function addCertification() {
        const input = document.getElementById('certInput');
        const value = input.value.trim();
        if (value) {
            const list = document.querySelector('.certifications-list');
            const item = document.createElement('div');
            item.className = 'cert-item';
            item.innerHTML = `<span>${value}</span><button type="button" class="cert-remove" onclick="removeCert(this)">×</button>`;
            list.appendChild(item);
            input.value = '';
        }
    }

    function removeCert(btn) {
        btn.parentElement.remove();
    }
</script>
@endsection