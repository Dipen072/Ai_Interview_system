@extends('layouts.app')

@slot('title')
    Edit Profile
@endslot

@section('content')
<div class="container-fluid" style="max-width: 950px;">
    <div class="mb-4">
        <h2 class="fw-bold m-0 text-white"><i class="bi bi-person-circle text-indigo me-2"></i> Account Profile</h2>
        <p class="text-muted-custom m-0">Update your avatar, profile data, or change your password credentials.</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show border-0 bg-success-subtle text-success-emphasis mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Profile updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show border-0 bg-success-subtle text-success-emphasis mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Password changed successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- 1. Profile Info Card -->
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-person me-2 text-info"></i> Profile Information</h5>
                <p class="text-muted-custom fs-7 mb-3" style="font-size: 0.8rem; line-height: 1.4;">Update your account's profile name, contact email address, and profile photo.</p>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <!-- Avatar View & Upload -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if($user->profile_photo)
                            <img src="{{ asset('uploads/profiles/' . $user->profile_photo) }}" alt="Avatar" class="rounded-circle border border-secondary" style="width: 75px; height: 75px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white font-weight-bold fs-3" style="width: 75px; height: 75px;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                        
                        <div class="flex-grow-1">
                            <label for="profile_photo" class="form-label text-white fw-bold mb-1" style="font-size: 0.85rem;">Upload Photo</label>
                            <input class="form-control form-control-sm @error('profile_photo') is-invalid @enderror" type="file" id="profile_photo" name="profile_photo" style="background-color: #1f2937; border-color: #374151; color: #fff;">
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="profile_name" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Name</label>
                        <input type="text" name="name" id="profile_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="profile_email" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Email Address</label>
                        <input type="email" name="email" id="profile_email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        Save Info
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Password Change Card -->
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-shield-lock me-2 text-info"></i> Update Password</h5>
                <p class="text-muted-custom fs-7 mb-3" style="font-size: 0.8rem; line-height: 1.4;">Ensure your account is using a long, random password to stay secure.</p>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label text-white fw-bold" style="font-size: 0.85rem;">New Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" required style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
