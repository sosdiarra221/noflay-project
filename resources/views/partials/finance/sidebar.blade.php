<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('finance.dashboard') }}" class="fs-18 fw-semibold">
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
                Finance
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('finance.dashboard') }}" class="pe-nav-link">
                    <i class="bi bi-speedometer2 pe-nav-icon"></i>
                    <span class="pe-nav-content">Vue d'ensemble</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('finance.revenus.index') }}" class="pe-nav-link">
                    <i class="bi bi-graph-up-arrow pe-nav-icon"></i>
                    <span class="pe-nav-content">Revenus</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('finance.reversements.index') }}" class="pe-nav-link">
                    <i class="bi bi-arrow-left-right pe-nav-icon"></i>
                    <span class="pe-nav-content">Reversements bailleurs</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('finance.taxes.index') }}" class="pe-nav-link">
                    <i class="bi bi-receipt pe-nav-icon"></i>
                    <span class="pe-nav-content">Taxes collectées</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('finance.cautions.index') }}" class="pe-nav-link">
                    <i class="bi bi-shield-lock pe-nav-icon"></i>
                    <span class="pe-nav-content">Cautions / garanties</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('finance.journal-caisse.index') }}" class="pe-nav-link">
                    <i class="bi bi-journal-text pe-nav-icon"></i>
                    <span class="pe-nav-content">Journal de caisse</span>
                </a>
            </li>
            <li class="pe-menu-title">
                Configuration
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
