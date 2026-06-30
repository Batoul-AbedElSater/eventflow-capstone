@extends('planner.settings.index')

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
            <p class="card-description">Choose how the interface should look</p>
            <div class="theme-selector">
                <label class="theme-option">
                    <input type="radio" name="theme_mode" value="light" {{ $preferences->theme_mode === 'light' ? 'checked' : '' }}>
                    <span class="theme-preview light-theme"></span>
                    <span class="theme-name">Light</span>
                    <p class="theme-desc">Bright and clean interface</p>
                </label>
                <label class="theme-option">
                    <input type="radio" name="theme_mode" value="dark" {{ $preferences->theme_mode === 'dark' ? 'checked' : '' }}>
                    <span class="theme-preview dark-theme"></span>
                    <span class="theme-name">Dark</span>
                    <p class="theme-desc">Easy on the eyes at night</p>
                </label>
                <label class="theme-option">
                    <input type="radio" name="theme_mode" value="auto" {{ $preferences->theme_mode === 'auto' ? 'checked' : '' }}>
                    <span class="theme-preview auto-theme"></span>
                    <span class="theme-name">Auto</span>
                    <p class="theme-desc">Follow system settings</p>
                </label>
            </div>
        </div>

        <!-- Color Scheme -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">🌈 Color Scheme</h3>
            <p class="card-description">Select your preferred accent color</p>
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

        <!-- Dashboard Layout -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">📊 Dashboard Layout</h3>
            <p class="card-description">Choose how your dashboard content is displayed</p>
            <div class="layout-selector">
                <label class="layout-option">
                    <input type="radio" name="dashboard_layout" value="grid" {{ $preferences->dashboard_layout === 'grid' ? 'checked' : '' }}>
                    <span class="layout-preview grid-layout">
                        <div class="layout-item"></div>
                        <div class="layout-item"></div>
                        <div class="layout-item"></div>
                        <div class="layout-item"></div>
                    </span>
                    <span class="layout-name">Grid</span>
                    <p class="layout-desc">Cards in a grid format</p>
                </label>
                <label class="layout-option">
                    <input type="radio" name="dashboard_layout" value="list" {{ $preferences->dashboard_layout === 'list' ? 'checked' : '' }}>
                    <span class="layout-preview list-layout">
                        <div class="layout-item"></div>
                        <div class="layout-item"></div>
                        <div class="layout-item"></div>
                    </span>
                    <span class="layout-name">List</span>
                    <p class="layout-desc">Items in a list format</p>
                </label>
                <label class="layout-option">
                    <input type="radio" name="dashboard_layout" value="compact" {{ $preferences->dashboard_layout === 'compact' ? 'checked' : '' }}>
                    <span class="layout-preview compact-layout">
                        <div class="layout-item small"></div>
                        <div class="layout-item small"></div>
                        <div class="layout-item small"></div>
                        <div class="layout-item small"></div>
                        <div class="layout-item small"></div>
                        <div class="layout-item small"></div>
                    </span>
                    <span class="layout-name">Compact</span>
                    <p class="layout-desc">Condensed view</p>
                </label>
            </div>
        </div>

        <!-- Font Size -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">📝 Font Size</h3>
            <p class="card-description">Adjust text size for better readability</p>
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

        <!-- Animations & Effects -->
        <div class="settings-card" style="border-top: 4px solid #FF9800;">
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

                <div class="animation-item">
                    <div class="animation-content">
                        <label class="animation-label">Hover Effects</label>
                        <p class="animation-description">Interactive hover effects on buttons and links</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="hover_effects" {{ $preferences->hover_effects ?? true ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="animation-item">
                    <div class="animation-content">
                        <label class="animation-label">Reduce Motion</label>
                        <p class="animation-description">Minimize animations for accessibility</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="reduce_motion" {{ $preferences->reduce_motion ?? false ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Language -->
        <div class="settings-card" style="border-top: 4px solid #2196F3;">
            <h3 class="card-title">🌐 Language</h3>
            <p class="card-description">Choose your preferred language</p>
            <select name="language" class="language-select">
                <option value="en" {{ $preferences->language === 'en' ? 'selected' : '' }}>English</option>
                <option value="ar" {{ $preferences->language === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                <option value="fr" {{ $preferences->language === 'fr' ? 'selected' : '' }}>Français (French)</option>
            </select>
        </div>

        <!-- Sidebar Preferences -->
        <div class="settings-card" style="border-top: 4px solid #9C27B0;">
            <h3 class="card-title">🎛️ Sidebar Preferences</h3>
            <div class="sidebar-group">
                <div class="sidebar-item">
                    <div class="sidebar-content">
                        <label class="sidebar-label">Collapse Sidebar by Default</label>
                        <p class="sidebar-description">Start with the sidebar minimized</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="collapse_sidebar" {{ $preferences->collapse_sidebar ?? false ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="sidebar-item">
                    <div class="sidebar-content">
                        <label class="sidebar-label">Sidebar Icons Only</label>
                        <p class="sidebar-description">Show only icons in the sidebar (save space)</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="sidebar_icons_only" {{ $preferences->sidebar_icons_only ?? false ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="settings-card preview-card">
            <h3 class="card-title">👀 Live Preview</h3>
            <div class="preview-box">
                <div class="preview-header" id="previewHeader">
                    <span>HAFLET FLOW</span>
                </div>
                <p class="preview-text">Dashboard Layout: <span id="previewLayout">Grid</span></p>
                <p class="preview-text">Theme: <span id="previewTheme">Auto</span></p>
                <p class="preview-text">Color: <span id="previewColor">Mixed</span></p>
                <button type="button" class="preview-button">Sample Button</button>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
            Save Appearance Settings
        </button>
    </form>
</div>

<style>
    :root {
        --garden-green: #475B35;
        --amnesiac-white: #F5F9E5;
        --coral-haze: #E19184;
        --calypso-berry: #C63E4E;
        --vampire-hunter: #620607;
        --cream: #EFE7DA;
    }

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
        margin-bottom: 16px;
    }

    .card-description {
        font-size: 13px;
        color: #999;
        margin-bottom: 16px;
    }

    /* Theme Selector */
    .theme-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
    }

    .theme-option {
        position: relative;
        cursor: pointer;
        text-align: center;
    }

    .theme-option input {
        display: none;
    }

    .theme-preview {
        display: block;
        width: 100%;
        height: 80px;
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
        font-weight: 700;
        color: #333;
        margin-bottom: 4px;
    }

    .theme-desc {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    .theme-option input:checked + .theme-preview {
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.2);
    }

    /* Color Selector */
    .color-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
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
        font-weight: 700;
        color: #333;
    }

    .color-option input:checked + .color-circle {
        border-color: #333;
        box-shadow: 0 0 0 2px white, 0 0 0 4px #333;
        transform: scale(1.1);
    }

    /* Layout Selector */
    .layout-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
    }

    .layout-option {
        position: relative;
        cursor: pointer;
        text-align: center;
    }

    .layout-option input {
        display: none;
    }

    .layout-preview {
        display: grid;
        width: 100%;
        height: 80px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 8px;
        gap: 4px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        background: white;
    }

    .grid-layout {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
    }

    .list-layout {
        grid-template-columns: 1fr;
        grid-template-rows: 1fr 1fr 1fr;
    }

    .compact-layout {
        grid-template-columns: repeat(3, 1fr);
        grid-template-rows: repeat(2, 1fr);
    }

    .layout-item {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        border-radius: 4px;
    }

    .layout-item.small {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
    }

    .layout-name {
        display: block;
        font-weight: 700;
        color: #333;
        margin-bottom: 4px;
    }

    .layout-desc {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    .layout-option input:checked + .layout-preview {
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.2);
    }

    /* Font Selector */
    .font-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
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
        font-weight: 700;
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

    .animation-item:last-child {
        border-bottom: none;
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

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 28px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(22px);
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
        background: white;
    }

    .language-select:focus {
        outline: none;
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.1);
    }

    /* Sidebar Group */
    .sidebar-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sidebar-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .sidebar-item:last-child {
        border-bottom: none;
    }

    .sidebar-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .sidebar-description {
        font-size: 13px;
        color: #999;
        margin: 0;
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

    .preview-header {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        font-weight: 900;
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 16px;
    }

    .preview-text {
        color: #666;
        margin: 8px 0;
        font-size: 14px;
    }

    .preview-button {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        margin-top: 16px;
    }

    @media (max-width: 768px) {
        .theme-selector {
            grid-template-columns: 1fr;
        }

        .color-selector {
            grid-template-columns: repeat(2, 1fr);
        }

        .layout-selector {
            grid-template-columns: 1fr;
        }

        .font-selector {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    function updateAppearance(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('appearanceForm'));
        
        fetch('{{ route("planner.settings.appearance") }}', {
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

    // Update preview
    document.querySelectorAll('input[name="dashboard_layout"]').forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('previewLayout').textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });
    });

    document.querySelectorAll('input[name="theme_mode"]').forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('previewTheme').textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });
    });

    document.querySelectorAll('input[name="color_scheme"]').forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('previewColor').textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });
    });
</script>
@endsection