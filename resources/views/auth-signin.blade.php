@extends('partials.layouts.master_auth')

@section('title', 'Connexion | Takha CRM')

@section('content')

    <!-- START -->
    <div>
        <img src="{{ asset('assets/images/auth/login_bg.jpg') }}" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="Logo" height="30"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-3 mb-8 fw-medium text-center">Connexion — Gestion Locative</h6>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <ul class="nav nav-pills nav-justified mb-6" id="ongletsConnexion" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ session('onglet') !== 'pin' ? 'active' : '' }}" id="tab-mdp" data-bs-toggle="tab" data-bs-target="#pane-mdp" type="button" role="tab">
                                        <i class="bi bi-key me-1"></i>Mot de passe
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ session('onglet') === 'pin' ? 'active' : '' }}" id="tab-pin" data-bs-toggle="tab" data-bs-target="#pane-pin" type="button" role="tab">
                                        <i class="bi bi-grid-3x3 me-1"></i>Code PIN
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade {{ session('onglet') !== 'pin' ? 'show active' : '' }}" id="pane-mdp" role="tabpanel">
                                    <form action="{{ route('login.store') }}" method="POST">
                                        @csrf
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <label for="identifiant" class="form-label">Email ou identifiant <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="identifiant" name="identifiant"
                                                    value="{{ old('identifiant') }}" placeholder="votre.email@agence.com ou identifiant" required autofocus>
                                            </div>
                                            <div class="col-12">
                                                <label for="password" class="form-label">Mot de passe <span
                                                        class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password" name="password"
                                                    placeholder="Votre mot de passe" required>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                                    <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-8">
                                                <button type="submit" class="btn btn-primary w-full mb-4">Se connecter<i
                                                        class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade {{ session('onglet') === 'pin' ? 'show active' : '' }}" id="pane-pin" role="tabpanel">
                                    <form action="{{ route('login.pin') }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="identifiant-pin" class="form-label">Email ou identifiant <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="identifiant-pin" name="identifiant"
                                                value="{{ old('identifiant') }}" placeholder="votre.email@agence.com ou identifiant" required>
                                        </div>
                                        <label class="form-label text-center d-block">Code PIN à 4 chiffres</label>
                                        @include('partials.pin-pad', ['champNom' => 'code_pin'])
                                        <button type="submit" class="btn btn-primary w-full mt-4">Se connecter<i
                                                class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                    </form>
                                </div>
                            </div>
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
