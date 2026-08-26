<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('central.dashboard') }}" class="fs-18 fw-semibold">
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
                Espace éditeur
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('central.dashboard') }}" class="pe-nav-link">
                    <i class="bi bi-speedometer2 pe-nav-icon"></i>
                    <span class="pe-nav-content">Tableau de bord</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('central.societes.index') }}" class="pe-nav-link">
                    <i class="bi bi-building pe-nav-icon"></i>
                    <span class="pe-nav-content">Sociétés</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('central.packages.index') }}" class="pe-nav-link">
                    <i class="bi bi-box-seam pe-nav-icon"></i>
                    <span class="pe-nav-content">Packages</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
