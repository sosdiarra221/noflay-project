@extends('partials.layouts.master')

@section('title', 'Mon profil')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{ $utilisateur->avatarUrl() }}" alt="Avatar" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                        <h5 class="mb-0">{{ $utilisateur->name }}</h5>
                        <p class="text-muted fs-13">{{ $utilisateur->role->libelle ?? '' }}</p>
                        <form action="{{ route('profil.update-photo') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <input type="file" class="form-control mb-2" name="photo" accept="image/*" required>
                            <button type="submit" class="btn btn-light-primary w-100"><i class="bi bi-upload me-1"></i>Changer la photo</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-4" id="ongletsProfil" role="tablist">
                            <li class="nav-item"><button class="nav-link {{ session('onglet', 'infos') === 'infos' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#pane-infos">Informations</button></li>
                            <li class="nav-item"><button class="nav-link {{ session('onglet') === 'pin' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#pane-pin">Code PIN</button></li>
                            <li class="nav-item"><button class="nav-link {{ session('onglet') === 'mot-de-passe' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#pane-mdp">Mot de passe</button></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade {{ session('onglet', 'infos') === 'infos' ? 'show active' : '' }}" id="pane-infos">
                                <form action="{{ route('profil.update-informations') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nom complet</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $utilisateur->name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email', $utilisateur->email) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Identifiant</label>
                                            <input type="text" class="form-control" name="identifiant" value="{{ old('identifiant', $utilisateur->identifiant) }}" placeholder="Optionnel">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade {{ session('onglet') === 'pin' ? 'show active' : '' }}" id="pane-pin">
                                <form action="{{ route('profil.update-code-pin') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label class="form-label">Mot de passe actuel (confirmation)<span class="text-danger ms-1">*</span></label>
                                        <input type="password" class="form-control" name="mot_de_passe_actuel" required>
                                    </div>
                                    <label class="form-label text-center d-block">Nouveau code PIN (4 chiffres)</label>
                                    @include('partials.pin-pad', ['champNom' => 'code_pin'])
                                    <label class="form-label text-center d-block mt-4">Confirmer le nouveau code PIN</label>
                                    @include('partials.pin-pad', ['champNom' => 'code_pin_confirmation'])
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary">Mettre à jour le code PIN</button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade {{ session('onglet') === 'mot-de-passe' ? 'show active' : '' }}" id="pane-mdp">
                                <form action="{{ route('profil.update-mot-de-passe') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Mot de passe actuel<span class="text-danger ms-1">*</span></label>
                                            <input type="password" class="form-control" name="mot_de_passe_actuel" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nouveau mot de passe<span class="text-danger ms-1">*</span></label>
                                            <input type="password" class="form-control" name="password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Confirmer le nouveau mot de passe<span class="text-danger ms-1">*</span></label>
                                            <input type="password" class="form-control" name="password_confirmation" required>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Mettre à jour le mot de passe</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
