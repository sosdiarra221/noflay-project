<!DOCTYPE html>
<html lang="fr">

<meta charset="utf-8" />
<title>@yield('title', 'Espace éditeur | Takha')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<link rel="shortcut icon" href="{{ asset('assets/images/k_favicon_32x.png') }}">

@include('partials.head-css')

<body>

<nav class="navbar navbar-expand navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-semibold" href="{{ route('central.societes.index') }}">
        <i class="bi bi-hdd-network me-2"></i>Takha — Espace éditeur
    </a>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="text-white-50 fs-13">{{ auth('admin')->user()->name }}</span>
        <form action="{{ route('central.logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </button>
        </form>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</div>

@include('partials.vendor-scripts')

@yield('js')

</body>

</html>
