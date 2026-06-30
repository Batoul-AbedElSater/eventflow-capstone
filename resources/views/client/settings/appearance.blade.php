@extends('client.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Appearance & Display</h2>
        <p class="section-subtitle">Customize how HAFLET FLOW looks and feels</p>
    </div>

    <form id="appearanceForm" onsubmit="updateAppearance(event)">
        <!-- Theme Mode -->
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">🎨 Theme Mode</h3>
            <div class="theme-selector">
                <label class="theme-option">
                    <input type="radio" name="theme_mode" value="light" {{ $preferences->theme_mode === 'light' ? 'checked' : '' }}>
                    <span class="theme-preview light-theme"></span>
                    <span class="theme-name">Light</span>
                </label>
                <label class="theme-option">
                    <input type="radio" name="theme_mode" value="dark" {{ $preferences->theme_mode === 'dark' ? 'checked' : '' }}>
                    <span class="theme-preview dark-theme"></span>
                    <span class="theme-name">Dark</span>
                </label>
                <label class="theme-option">
                    <input type="radio" name="theme_mode" value="auto" {{ $preferences->theme_mode === 'auto' ? 'checked' : '' }}>
                    <span class="theme-preview auto-theme"></span>
                    <span class="theme-name">Auto (System)</span>
                </label>
            </div>
        </div>

        <!-- Color Scheme -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">🌈 Color Scheme</h3>
            <div class="color-selector">
                <label class="color-option">
                    <input type="radio" name="color_scheme" value="coral" {{ $preferences->color_scheme === 'coral' ? 'checked' : '' }}>
                    <span class="color-circle" style="background: #E19184;"></span>
                    <span class="color-name">Coral</span>
                </label>
                <label class="color-option">
                    <input type="radio" name="color_scheme" value="berry" {{ $preferences->color_scheme === 'berry' ? 'checked' : '' }}>
                    <span class="color-circle" style="background: #C63E4E;"></span>
                    <span class="color-name">Berry</span>
                </label>
                <label class="color-option">
                    <input type="radio" name="color_scheme" value="green" {{ $preferences->color_scheme === 'green' ? 'checked' : '' }}>
                    <span class="color-circle" style="background: #475B35;"></span>
                    <span class="color-name">Green</span>
                </label>
                <label class="color-option">
                    <input type="radio" name="color_scheme" value="mixed" {{ $preferences->color_scheme === 'mixed' ? 'checked' : '' }}>
                    <span class="color-circle mixed-gradient"></span>
                    <span class="color-name">Mixed</span>
                </label>
            </div>
        </div>

        <!-- Font Size -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">📝 Font Size</h3>
            <div class="font-selector">
                <label class="font-option">
                    <input type="radio" name="font_size" value="small" {{ $preferences->font_size === 'small' ? 'checked' : '' }}>
                    <span class="font-preview small-font">Small</span>
                </label>
                <label class="font-option">
                    <input type="radio" name="font_size" value="medium" {{ $preferences->font_size === 'medium' ? 'checked' : '' }}>
                    <span class="font-preview medium-font">Medium</span>
                </label>
                <label class="font-option">
                    <input type="radio" name="font_size" value="large" {{ $preferences->font_size === 'large' ? 'checked' : '' }}>
                    <span class="font-preview large-font">Large</span>
                </label>
            </div>
        </div>

        <!-- Animation -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">✨ Animations & Effects</h3>
            <div class="animation-group">
                <div class="animation-item">
                    <div class="animation-content">
                        <label class="animation-label">Enable Animations</label>
                        <p class="animation-description">Smooth transitions and animations throughout the app</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="animations" {{ $preferences->animations ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Language -->
        <div class="settings-card" style="border-top: 4px solid #FF6B6B;">
            <h3 class="card-title">🌐 Language</h3>
            <select name="language" class="language-select">
                <option value="en" {{ $preferences->language === 'en' ? 'selected' : '' }}>English</option>
                <option value="ar" {{ $preferences->language === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                <option value="fr" {{ $preferences->language === 'fr' ? 'selected' : '' }}>Français (French)</option>
            </select>
        </div>

        <!-- Preview -->
        <div class="settings-card preview-card">
            <h3 class="card-title">👀 Preview</h3>
            <div class="preview-box">
                <p class="preview-text">This is how your dashboard will look</p>
                <button type="button" class="preview-button">Sample Button</button>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
            Save Appearance
        </button>
    </form>
</div>

<style>
    /* Theme Selector */
    .theme-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .theme-option {
        position: relative;
        cursor: pointer;
    }

    .theme-option input {
        display: none;
    }

    .theme-preview {
        display: block;
        width: 100%;
        height: 100px;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }

    .light-theme {
        background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%);
    }

    .dark-theme {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    }

    .auto-theme {
        background: linear-gradient(90deg, #ffffff 0%, #ffffff 50%, #1a1a1a 50%, #1a1a1a 100%);
    }

    .theme-name {
        display: block;
        text-align: center;
        font-weight: 600;
        color: #333;
    }

    .theme-option input:checked + .theme-preview {
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.2);
    }

    /* Color Selector */
    .color-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 16px;
    }

    .color-option {
        position: relative;
        cursor: pointer;
        text-align: center;
    }

    .color-option input {
        display: none;
    }

    .color-circle {
        display: inline-block;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid transparent;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .mixed-gradient {
        background: linear-gradient(135deg, #E19184 0%, #C63E4E 50%, #475B35 100%) !important;
    }

    .color-name {
        display: block;
        font-weight: 600;
        color: #333;
    }

    .color-option input:checked + .color-circle {
        border-color: #333;
        box-shadow: 0 0 0 2px white, 0 0 0 4px #333;
        transform: scale(1.1);
    }

    /* Font Selector */
    .font-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
    }

    .font-option {
        position: relative;
        cursor: pointer;
    }

    .font-option input {
        display: none;
    }

    .font-preview {
        display: block;
        padding: 20px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        color: #333;
        transition: all 0.3s ease;
        background: white;
    }

    .small-font {
        font-size: 12px;
    }

    .medium-font {
        font-size: 16px;
    }

    .large-font {
        font-size: 20px;
    }

    .font-option input:checked + .font-preview {
        border-color: var(--coral-haze);
        background: rgba(225, 145, 132, 0.1);
    }

    /* Animation Group */
    .animation-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .animation-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .animation-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .animation-description {
        font-size: 13px;
        color: #999;
        margin: 0;
    }

    /* Language Select */
    .language-select {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .language-select:focus {
        outline: none;
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.1);
    }

    /* Preview Card */
    .preview-card {
        background: linear-gradient(135deg, rgba(225, 145, 132, 0.1) 0%, rgba(198, 62, 78, 0.1) 100%);
    }

    .preview-box {
        padding: 24px;
        background: white;
        border-radius: 8px;
        text-align: center;
    }

    .preview-text {
        color: #666;
        margin-bottom: 16px;
    }

    .preview-button {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
</style>

<script>
    function updateAppearance(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('appearanceForm'));
        
        fetch('{{ route("client.settings.appearance") }}', {
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
</script>
@endsection