<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white">Verify Email</h4>
        <p class="text-muted-custom" style="font-size: 0.85rem; line-height: 1.4;">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success border-0 bg-success-subtle text-success-emphasis mb-3 text-center" role="alert" style="font-size: 0.85rem;">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="mt-4 d-flex flex-column gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary w-100 py-2.5 fw-bold">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
