<aside class="pe-app-sidebar" id="sidebar">
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ route('locative.dashboard') }}" class="fs-18 fw-semibold">
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
                Locative
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.dashboard') }}" class="pe-nav-link">
                    <i class="bi bi-speedometer2 pe-nav-icon"></i>
                    <span class="pe-nav-content">Vue d'ensemble</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.bailleurs.index') }}" class="pe-nav-link">
                    <i class="bi bi-person-badge pe-nav-icon"></i>
                    <span class="pe-nav-content">Bailleurs</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.gerances.index') }}" class="pe-nav-link">
                    <i class="bi bi-file-earmark-ruled pe-nav-icon"></i>
                    <span class="pe-nav-content">Gérances</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.biens.index') }}" class="pe-nav-link">
                    <i class="bi bi-building pe-nav-icon"></i>
                    <span class="pe-nav-content">Biens</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.locataires.index') }}" class="pe-nav-link">
                    <i class="bi bi-people pe-nav-icon"></i>
                    <span class="pe-nav-content">Locataires</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.locations.index') }}" class="pe-nav-link">
                    <i class="bi bi-key pe-nav-icon"></i>
                    <span class="pe-nav-content">Locations</span>
                </a>
            </li>
            <li class="pe-menu-title">
                Finances
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.echeances.index') }}" class="pe-nav-link">
                    <i class="bi bi-calendar-check pe-nav-icon"></i>
                    <span class="pe-nav-content">Loyers</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.encaissements.index') }}" class="pe-nav-link">
                    <i class="bi bi-cash-stack pe-nav-icon"></i>
                    <span class="pe-nav-content">Encaissements</span>
                </a>
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.reversements.index') }}" class="pe-nav-link">
                    <i class="bi bi-arrow-left-right pe-nav-icon"></i>
                    <span class="pe-nav-content">Reversements</span>
                </a>
            </li>
            @canany(['locative.corbeille', 'locative.journal'])
                <li class="pe-menu-title">
                    Administration
                </li>
                @can('locative.corbeille')
                    <li class="pe-slide pe-has-sub">
                        <a href="{{ route('locative.corbeille.index') }}" class="pe-nav-link">
                            <i class="bi bi-trash3 pe-nav-icon"></i>
                            <span class="pe-nav-content">Corbeille</span>
                        </a>
                    </li>
                @endcan
                @can('locative.journal')
                    <li class="pe-slide pe-has-sub">
                        <a href="{{ route('locative.journal.index') }}" class="pe-nav-link">
                            <i class="bi bi-journal-text pe-nav-icon"></i>
                            <span class="pe-nav-content">Journal d'activité</span>
                        </a>
                    </li>
                @endcan
            @endcanany
            <li class="pe-menu-title">
                Configuration
            </li>
            <li class="pe-slide pe-has-sub">
                <a href="{{ route('locative.parametres.index') }}" class="pe-nav-link">
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
