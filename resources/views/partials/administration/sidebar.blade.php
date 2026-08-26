<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('administration.dashboard') }}" class="fs-18 fw-semibold">
            <img height="30" class="pe-app-sidebar-logo-default d-none" alt="Logo" src="{{ asset('assets/images/logo-dark.png') }}">
            <img height="30" class="pe-app-sidebar-logo-light d-none" alt="Logo" src="{{ asset('assets/images/logo-light.png') }}">
            <img height="30" class="pe-app-sidebar-logo-minimize d-none" alt="Logo" src="{{ asset('assets/images/logo-md.png') }}">
            <img height="30" class="pe-app-sidebar-logo-minimize-light d-none" alt="Logo" src="{{ asset('assets/images/logo-md-light.png') }}">
        </a>
        <!--end::Brand Image-->
    </div>
    <nav class="pe-app-sidebar-menu nav nav-pills" data-simplebar id="sidebar-simplebar">
        <ul class="pe-main-menu list-unstyled">
            <li class="pe-menu-title">
                Direction &amp; Administration
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('administration.dashboard') }}" class="pe-nav-link">
                    <i class="bi bi-speedometer2 pe-nav-icon"></i>
                    <span class="pe-nav-content">Vue d'ensemble</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('administration.utilisateurs.index') }}" class="pe-nav-link">
                    <i class="bi bi-people pe-nav-icon"></i>
                    <span class="pe-nav-content">Utilisateurs</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('administration.roles.index') }}" class="pe-nav-link">
                    <i class="bi bi-shield-check pe-nav-icon"></i>
                    <span class="pe-nav-content">Rôles &amp; permissions</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('administration.securite.index') }}" class="pe-nav-link">
                    <i class="bi bi-lock pe-nav-icon"></i>
                    <span class="pe-nav-content">Sécurité &amp; session</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('administration.modules.index') }}" class="pe-nav-link">
                    <i class="bi bi-grid pe-nav-icon"></i>
                    <span class="pe-nav-content">Modules</span>
                </a>
            </li>
            <li class="pe-menu-title">
                Paramétrage général
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('reglages.index') }}" class="pe-nav-link">
                    <i class="bi bi-gear pe-nav-icon"></i>
                    <span class="pe-nav-content">Réglages de la société</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('departements.index') }}" class="pe-nav-link">
                    <i class="bi bi-diagram-3 pe-nav-icon"></i>
                    <span class="pe-nav-content">Départements</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('reglages.index') }}#devise-tab-pane" class="pe-nav-link">
                    <i class="bi bi-cash-coin pe-nav-icon"></i>
                    <span class="pe-nav-content">Devises</span>
                </a>
            </li>
            <li class="pe-menu-title">
                Configuration
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('administration.jours-feries.index') }}" class="pe-nav-link">
                    <i class="bi bi-calendar-x pe-nav-icon"></i>
                    <span class="pe-nav-content">Jours fériés</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('administration.types-absence.index') }}" class="pe-nav-link">
                    <i class="bi bi-card-list pe-nav-icon"></i>
                    <span class="pe-nav-content">Types d'absence</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ url('/') }}" class="pe-nav-link">
                    <i class="bi bi-box-arrow-left pe-nav-icon"></i>
                    <span class="pe-nav-content">Retour à l'application</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
