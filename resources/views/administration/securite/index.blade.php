@extends('partials.layouts.master-administration')

@section('title', 'Sécurité & session | Direction & Administration')
@section('title-sub', 'Direction & Administration')
@section('pagetitle', 'Sécurité & session')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Déconnexion automatique pour inactivité</h6></div>
            <div class="card-body">
                <p class="text-muted">
                    Lorsqu'un utilisateur reste inactif au-delà de cette durée, sa session est automatiquement fermée et il est
                    redirigé vers un écran de verrouillage lui demandant uniquement son code PIN pour reprendre sa session.
                </p>
                <form action="{{ route('administration.securite.update') }}" method="POST" class="row g-4">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="form-label">Durée d'inactivité avant déconnexion (minutes)<span class="text-danger ms-1">*</span></label>
                        <input type="number" min="1" max="480" class="form-control" name="duree_inactivite_minutes" value="{{ old('duree_inactivite_minutes', $reglage->duree_inactivite_minutes) }}" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Méthodes de connexion disponibles</h6></div>
            <div class="card-body">
                <ul class="fs-13">
                    <li>Email ou identifiant + mot de passe.</li>
                    <li>Email ou identifiant + code PIN à 4 chiffres.</li>
                    <li>Après verrouillage pour inactivité : code PIN uniquement, sans ressaisir l'identifiant.</li>
                </ul>
                <p class="text-muted fs-12 mb-0">Chaque utilisateur configure son propre code PIN et mot de passe depuis <a href="{{ route('profil.index') }}">son profil</a>, ou un administrateur peut les définir depuis la fiche utilisateur.</p>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
