@extends($layout)

@section('title', $roleLabel . ' Profile')

@section('content')

@php
    $avatar = $user->avatar_url
        ? asset('storage/' . $user->avatar_url)
        : asset('images/default-avatar.png');
@endphp

<div class="profile-container">

    <!-- HEADER -->
    <div class="page-header">
        <h1>My Profile</h1>
        <p class="subtitle">Manage your personal information</p>
    </div>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- PROFILE CARD -->
    <div class="profile-card">
        <div class="profile-header">

            <div class="profile-avatar-wrapper">

                <!-- CLICKABLE AVATAR -->
                <label for="avatar" class="profile-avatar-large">

                    <img id="avatarPreview"
                         src="{{ $avatar }}"
                         alt="Profile Photo"
                         style="cursor:pointer;">

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

    <!-- FORM -->
    <div class="form-section">

        <form action="{{ route('client.profile.update') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <!-- IMPORTANT: MUST BE OUTSIDE LABEL -->
            <input type="file" id="avatar" name="avatar" hidden>

            <div class="form-grid">

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name"
                           value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $user->phone) }}">
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>

        </form>

    </div>

</div>

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<script>
const avatarInput = document.getElementById('avatar');
const avatarPreview = document.getElementById('avatarPreview');

if (avatarInput) {
    avatarInput.addEventListener('change', function () {
        if (!this.files.length) return;

        const reader = new FileReader();
        reader.onload = e => avatarPreview.src = e.target.result;
        reader.readAsDataURL(this.files[0]);
    });
}
</script>

@endsection
