<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top px-3">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center fw-bold text-uppercase tracking-wider" href="{{ route('dashboard') }}" style="font-size: 1.25rem;">
            <i class="bi bi-cpu text-info me-2 fs-4"></i>
            <span>AI Interview <span class="text-info">Prep</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            @auth
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none dropdown-toggle text-light d-flex align-items-center p-0 border-0" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset('uploads/profiles/' . auth()->user()->profile_photo) }}" alt="Avatar" class="rounded-circle me-2 border border-secondary" style="width: 32px; height: 32px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white font-weight-bold me-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary text-light mt-2" aria-labelledby="userMenuButton">
                        <li>
                            <a class="dropdown-item text-light hover-bg-dark" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider border-secondary">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</nav>

<style>
    .dropdown-item:hover {
        background-color: var(--border-color) !important;
        color: #fff !important;
    }
</style>
