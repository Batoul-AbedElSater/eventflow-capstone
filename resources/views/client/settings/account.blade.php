@extends('client.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Account Settings</h2>
        <p class="section-subtitle">Manage your profile information and security</p>
    </div>

    <!-- Profile Photo -->
    <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
        <h3 class="card-title">Profile Photo</h3>
        <div class="photo-upload-area">
            <div class="photo-preview">
                <img src="{{ Auth::user()->getAvatarUrlAttribute() }}" alt="Profile" id="photoImg">
            </div>
            <div class="photo-upload-input">
                <input type="file" id="photoInput" accept="image/*" style="display: none;">
                <button type="button" class="btn-upload" onclick="document.getElementById('photoInput').click()">
                    <i class="fas fa-camera"></i> Upload Photo
                </button>
                <p class="upload-hint">JPG, PNG or GIF (Max 5MB)</p>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
        <h3 class="card-title">Personal Information</h3>
        <form id="profileForm" onsubmit="updateProfile(event)">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="{{ auth()->user()->phone }}">
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" rows="4" placeholder="Tell us about yourself...">{{ auth()->user()->bio }}</textarea>
            </div>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
        <h3 class="card-title">Change Password</h3>
        <form id="passwordForm" onsubmit="changePassword(event)">
            @csrf
            <div class="form-group password-group">
                <label>Current Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="current_password" id="currentPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('currentPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-group password-group">
                <label>New Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="new_password" id="newPassword" required minlength="8">
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('newPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <p class="password-hint">At least 8 characters</p>
            </div>
            <div class="form-group password-group">
                <label>Confirm Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="new_password_confirmation" id="confirmPassword" required>
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirmPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-save">
                <i class="fas fa-key"></i> Update Password
            </button>
        </form>
    </div>
</div>

<style>
    .settings-section { display: flex; flex-direction: column; gap: 32px; }
    .section-header { margin-bottom: 24px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 900; margin-bottom: 8px; }
    .section-subtitle { color: #999; font-size: 14px; }
    .settings-card {
        background: linear-gradient(135deg, rgba(245,249,229,0.5) 0%, rgba(239,231,218,0.5) 100%);
        border-radius: 12px;
        padding: 32px;
        transition: all 0.3s ease;
    }
    .settings-card:hover { box-shadow: 0 8px 30px rgba(71, 91, 53, 0.1); }
    .card-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 24px; }
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .form-group input:focus, .form-group textarea:focus {
        outline: none;
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225,145,132,0.1);
    }
    .password-group .password-input-wrapper {
        position: relative;
    }
    .password-input-wrapper input {
        padding-right: 45px;
    }
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #999;
        font-size: 18px;
        padding: 6px;
    }
    .password-toggle:hover { color: var(--coral-haze); }
    .password-hint { font-size: 12px; color: #999; margin-top: 4px; }
    .photo-upload-area {
        display: flex;
        gap: 32px;
        align-items: center;
    }
    .photo-preview {
        flex-shrink: 0;
    }
    .photo-preview img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--coral-haze);
    }
    .btn-upload {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
    .btn-upload:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(225,145,132,0.3); }
    .upload-hint { font-size: 12px; color: #999; margin-top: 8px; }
    .btn-save {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(225,145,132,0.4); }
    @media (max-width: 768px) { .photo-upload-area { flex-direction: column; gap: 16px; } }
</style>

<script>
    function updateProfile(e) {
        e.preventDefault();
        const form = document.getElementById('profileForm');
        const formData = new FormData(form);

        fetch('{{ route("client.settings.profile") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                let msg = data.message || 'Update failed';
                if (data.errors) {
                    msg += '\n' + Object.values(data.errors).flat().join('\n');
                }
                alert(msg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please check the console.');
        });
    }

    function changePassword(e) {
        e.preventDefault();
        const form = document.getElementById('passwordForm');
        const formData = new FormData(form);

        fetch('{{ route("client.settings.password") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                form.reset();
            } else {
                let msg = data.message || 'Password change failed';
                if (data.errors) {
                    msg += '\n' + Object.values(data.errors).flat().join('\n');
                }
                alert(msg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please check the console.');
        });
    }

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.parentElement.querySelector('.password-toggle i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('photo', file);

        fetch('{{ route("client.settings.photo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('photoImg').src = data.url + '?t=' + Date.now(); // cache buster
                alert(data.message);
            } else {
                alert(data.message || 'Upload failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please check the console.');
        });
    });
</script>
@endsection