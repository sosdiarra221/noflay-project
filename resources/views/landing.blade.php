@extends('partials.layouts.master_auth')

@section('title', ($reglage->nom_societe ?: config('app.name')).' | Bienvenue')

@section('content')

    <div>
        <img src="{{ asset('assets/images/auth/login_bg.jpg') }}" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8 text-center">
                            @if ($reglage->logo)
                                <img src="{{ asset($reglage->logo) }}" alt="Logo" height="60" class="mb-4 mx-auto d-block">
                            @else
                                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="Logo" height="30" class="mb-4 mx-auto d-block">
                            @endif

                            <h4 class="mb-2 fw-semibold">{{ $reglage->nom_societe ?: config('app.name') }}</h4>
                            <p class="text-muted mb-8">Plateforme de gestion — connectez-vous pour accéder à votre espace de travail.</p>

                            <a href="{{ route('login') }}" class="btn btn-primary w-full">
                                Se connecter<i class="bi bi-box-arrow-in-right ms-1 fs-16"></i>
                            </a>
                        </div>
                    </div>
                    <p class="position-relative text-center fs-12 mb-0">© 2025 Takha CRM. Conception par Pene Technologies Service (PTS)</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
