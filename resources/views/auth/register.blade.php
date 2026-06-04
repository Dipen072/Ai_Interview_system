<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white">Create Account</h4>
        <p class="text-muted-custom" style="font-size: 0.9rem;">Register to begin taking free AI mock interviews.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Full Name</label>
            <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Email Address</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com">
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

        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold mb-3">
            Sign Up
        </button>

        <div class="text-center text-muted-custom" style="font-size: 0.85rem;">
            Already have an account? <a href="{{ route('login') }}" class="text-link">Sign In</a>
        </div>
    </form>
</x-guest-layout>
