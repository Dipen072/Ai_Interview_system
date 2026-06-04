<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white">Create New Password</h4>
        <p class="text-muted-custom" style="font-size: 0.9rem;">Fill in the credentials below to secure your account.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Email Address</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="name@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Password</label>
            <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Confirm Password</label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold">
            Reset Password
        </button>
    </form>
</x-guest-layout>
