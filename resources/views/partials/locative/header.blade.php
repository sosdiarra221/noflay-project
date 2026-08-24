<!-- Begin Header -->
<header class="app-header" id="appHeader">
    <div class="container-fluid w-100">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <div class="d-inline-flex align-items-center gap-5">
                    <a href="{{ route('locative.dashboard') }}" class="fs-18 fw-semibold">
                        <img height="30" class="header-sidebar-logo-default d-none" alt="Logo" src="{{ asset('assets/images/logo-dark.png') }}">
                        <img height="30" class="header-sidebar-logo-light d-none" alt="Logo" src="{{ asset('assets/images/logo-light.png') }}">
                        <img height="30" class="header-sidebar-logo-small d-none" alt="Logo" src="{{ asset('assets/images/logo-md.png') }}">
                        <img height="30" class="header-sidebar-logo-small-light d-none" alt="Logo" src="{{ asset('assets/images/logo-md-light.png') }}">
                    </a>
                    <button type="button" class="vertical-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill" id="toggleSidebar">
                        <i class="bi bi-arrow-bar-left header-icon"></i>
                    </button>
                    <div class="header-dropdown d-flex align-items-center">
                        <span class="badge bg-primary-subtle text-primary fs-13 px-3 py-2">
                            <i class="bi bi-building me-1"></i> Module Locative
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 d-flex align-items-center gap-1">
                <div class="dark-mode-btn" id="toggleMode">
                    <button class="btn header-btn active" id="lightModeBtn">
                        <i class="bi bi-brightness-high"></i>
                    </button>
                    <button class="btn header-btn" id="darkModeBtn">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>
                @include('partials.notifications-bell')
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="header-profile-btn btn gap-1 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-btn btn position-relative">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="Avatar Image" class="img-fluid rounded-circle">
                        </span>
                        <div class="d-none d-lg-block pe-2">
                            <span class="d-block mb-0 fs-13 fw-semibold">{{ auth()->user()->name ?? 'Utilisateur' }}</span>
                            <span class="d-block mb-0 fs-12 text-muted">{{ auth()->user()->role->libelle ?? 'Agence' }}</span>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-mega-sm header-dropdown-menu p-3">
                        <ul class="list-unstyled mb-1 border-bottom pb-1">
                            <li><a class="dropdown-item" href="{{ route('profil.index') }}"><i class="bi bi-person me-1"></i> Mon profil</a></li>
                            <li><a class="dropdown-item" href="{{ url('/') }}"><i class="bi bi-box-arrow-left me-1"></i> Retour à l'application</a></li>
                        </ul>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-1"></i> Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END Header -->
