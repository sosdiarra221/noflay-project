<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('rh.dashboard') }}" class="fs-18 fw-semibold">
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
                RH
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('rh.dashboard') }}" class="pe-nav-link">
                    <i class="bi bi-speedometer2 pe-nav-icon"></i>
                    <span class="pe-nav-content">Vue d'ensemble</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('rh.employes.index') }}" class="pe-nav-link">
                    <i class="bi bi-people pe-nav-icon"></i>
                    <span class="pe-nav-content">Employés</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('rh.contrats.index') }}" class="pe-nav-link">
                    <i class="bi bi-file-earmark-text pe-nav-icon"></i>
                    <span class="pe-nav-content">Contrats de travail</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('rh.affectations.index') }}" class="pe-nav-link">
                    <i class="bi bi-signpost-split pe-nav-icon"></i>
                    <span class="pe-nav-content">Affectations</span>
                </a>
            </li>
            <li class="pe-menu-title">
                Configuration
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('rh.sites.index') }}" class="pe-nav-link">
                    <i class="bi bi-geo-alt pe-nav-icon"></i>
                    <span class="pe-nav-content">Sites</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('rh.postes.index') }}" class="pe-nav-link">
                    <i class="bi bi-briefcase pe-nav-icon"></i>
                    <span class="pe-nav-content">Postes</span>
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
