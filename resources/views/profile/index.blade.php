@extends($layout)

@section('title', $roleLabel . ' Profile')

@section('content')
<div class="profile-container">

    <!-- Header -->
    <div class="page-header">
        <h1>My Profile</h1>
        <p class="subtitle">Manage your personal information</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-header">
          @php
                $avatar = $user->avatar_url
                    ? asset('storage/' . $user->avatar_url)
                    : asset('images/default-avatar.png');
        @endphp

<div class="profile-avatar-wrapper">

    <label for="avatar" class="profile-avatar-large">

        @if($avatar)
            <img
                id="avatarPreview"
                src="{{ $avatar }}"
                alt="Profile Photo">
       @else
            <img
                id="avatarPreview"
                src="{{ asset('images/default-avatar.png') }}"
                alt="Default Profile">
        @endif

        <div class="avatar-overlay">
            <i class="fas fa-camera"></i>
            <span>Add Photo</span>
        </div>

    </label>

</div>
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <p class="role-badge">
                    <i class="fas fa-user"></i> {{ $roleLabel }}
                </p>
                <p class="member-since">
                    Member since {{ $user->created_at->format('F Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Profile Information Form -->
    <div class="form-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-user-edit"></i>
                Personal Information
            </h3>
        </div>

       <form
            action="{{ route($routePrefix . '.profile.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="profile-form">
            @csrf
            @method('PUT')
            <input
                type="file"
                id="avatar"
                name="avatar"
                accept="image/*"
                hidden>

            <div class="form-grid">
                <!-- Name -->
                <div class="form-group">
                    <label for="name">
                        Full Name <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        Email Address <span class="required">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="+1 (555) 123-4567">
                    @error('phone')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="form-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-lock"></i>
                Change Password
            </h3>
        </div>

        <form action="{{ route($routePrefix . '.profile.password') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- Current Password -->
              <div class="form-group password-group">
                <label for="current_password">
                    Current Password <span class="required">*</span>
                </label>

                <div class="password-wrapper">
                    <input type="password" id="current_password" name="current_password" required>

                    <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                </div>

                @error('current_password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

                <!-- New Password -->
              <div class="form-group password-group">
                <label for="new_password">
                    New Password <span class="required">*</span>
                </label>

                <div class="password-wrapper">
                    <input type="password" id="new_password" name="new_password" required>

                    <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                </div>

                <small class="form-hint">Minimum 8 characters</small>
            </div>

                <!-- Confirm Password -->
                <div class="form-group password-group">
                    <label for="new_password_confirmation">
                        Confirm New Password <span class="required">*</span>
                    </label>

                    <div class="password-wrapper">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>

                        <i class="fas fa-eye toggle-password" data-target="new_password_confirmation"></i>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-key"></i> Update Password
                </button>
            </div>
        </form>
    </div>

</div>

<link rel="stylesheet" href="{{ asset('css/client-profile.css') }}">
<script>
const avatarInput = document.getElementById('avatar');
const avatarPreview = document.getElementById('avatarPreview');

avatarInput.addEventListener('change', function () {

    if (!this.files.length) return;

    const reader = new FileReader();

    reader.onload = function (e) {
        avatarPreview.src = e.target.result;
    };

    reader.readAsDataURL(this.files[0]);
});
</script>
<script>
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function () {

        const input = document.getElementById(this.dataset.target);

        if (input.type === 'password') {
            input.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
});
</script>
@endsection
