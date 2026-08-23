<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('commercial.dashboard') }}" class="fs-18 fw-semibold">
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
                Commercial
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('commercial.dashboard') }}" class="pe-nav-link">
                    <i class="bi bi-speedometer2 pe-nav-icon"></i>
                    <span class="pe-nav-content">Vue d'ensemble</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('commercial.prospects.index') }}" class="pe-nav-link">
                    <i class="bi bi-bullseye pe-nav-icon"></i>
                    <span class="pe-nav-content">Prospection</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('commercial.partenaires') }}" class="pe-nav-link">
                    <i class="bi bi-people pe-nav-icon"></i>
                    <span class="pe-nav-content">Partenaires</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('commercial.biens-disponibles') }}" class="pe-nav-link">
                    <i class="bi bi-house-check pe-nav-icon"></i>
                    <span class="pe-nav-content">Biens disponibles</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('commercial.agenda') }}" class="pe-nav-link">
                    <i class="bi bi-calendar-week pe-nav-icon"></i>
                    <span class="pe-nav-content">Agenda</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('commercial.rapports') }}" class="pe-nav-link">
                    <i class="bi bi-graph-up-arrow pe-nav-icon"></i>
                    <span class="pe-nav-content">Rapports</span>
                </a>
            </li>
            <li class="pe-menu-title">
                Configuration
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('commercial.parametres.index') }}" class="pe-nav-link">
                    <i class="bi bi-gear pe-nav-icon"></i>
                    <span class="pe-nav-content">Paramètres</span>
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
