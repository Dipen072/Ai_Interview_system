<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white">Sign In</h4>
        <p class="text-muted-custom" style="font-size: 0.9rem;">Access your dashboard and resume mock interviews.</p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success border-0 bg-success-subtle text-success-emphasis mb-3" role="alert" style="font-size: 0.85rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Email Address</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label text-white fw-bold m-0" style="font-size: 0.85rem;">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-link" href="{{ route('password.request') }}" style="font-size: 0.8rem;">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me Checkbox -->
        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember" style="background-color: #1f2937; border-color: #374151;">
            <label for="remember_me" class="form-check-label text-muted-custom" style="font-size: 0.85rem;">Remember my session</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold mb-3">
            Log In
        </button>

        <div class="text-center text-muted-custom" style="font-size: 0.85rem;">
            Don't have an account? <a href="{{ route('register') }}" class="text-link">Sign Up</a>
        </div>
    </form>
</x-guest-layout>
