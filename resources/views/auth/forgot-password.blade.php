<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white">Reset Password</h4>
        <p class="text-muted-custom" style="font-size: 0.85rem; line-height: 1.4;">Enter your registered email address and we'll send you a password reset link.</p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success border-0 bg-success-subtle text-success-emphasis mb-3" role="alert" style="font-size: 0.85rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label text-white fw-bold" style="font-size: 0.85rem;">Email Address</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold mb-3">
            Email Password Reset Link
        </button>

        <div class="text-center text-muted-custom" style="font-size: 0.85rem;">
            Remember your credentials? <a href="{{ route('login') }}" class="text-link">Sign In</a>
        </div>
    </form>
</x-guest-layout>
