@extends('layouts.app')

@slot('title')
    Edit User Roles
@endslot

@section('content')
<div class="container-fluid" style="max-width: 650px;">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left me-1"></i> Back to User Directory
        </a>
        <h2 class="fw-bold text-white"><i class="bi bi-person-gear text-indigo me-2"></i> Edit User & Roles</h2>
        <p class="text-muted-custom">Adjust profile details and system access permissions for this user.</p>
    </div>

    <div class="card p-4">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label text-white fw-bold">Full Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label text-white fw-bold">Email Address</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Roles Selection -->
            <div class="mb-4">
                <label class="form-label text-white fw-bold d-block">System Roles</label>
                <div class="p-3 rounded bg-dark border border-secondary">
                    @foreach($roles as $role)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" 
                                {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                            <label class="form-check-label text-white fw-medium" for="role_{{ $role->id }}">
                                {{ $role->name }}
                                <span class="d-block text-muted-custom fs-7" style="font-size: 0.75rem;">
                                    @if($role->name === 'Admin')
                                        Full access to categories, system logs, AI engine toggles, and user directory auditing.
                                    @else
                                        Access to mock interviews, history sheets, personal certificates, and reports dashboard.
                                    @endif
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('roles')
                    <div class="text-danger mt-2" style="font-size: 0.85rem;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                <i class="bi bi-check2-circle me-1"></i> Update User Details
            </button>
        </form>
    </div>
</div>
@endsection
