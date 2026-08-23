@extends('partials.layouts.master-locative')

@section('title', 'Journal d\'activité | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Journal d\'activité')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form method="GET" class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Journal d'activité <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $entrees->count() }}</span>
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <select class="form-select" name="user_id" onchange="this.form.submit()">
                                    <option value="">Tous les utilisateurs</option>
                                    @foreach ($utilisateurs as $utilisateur)
                                        <option value="{{ $utilisateur->id }}" @selected(request('user_id') == $utilisateur->id)>{{ $utilisateur->name }}</option>
                                    @endforeach
                                </select>
                                <select class="form-select" name="action" onchange="this.form.submit()">
                                    <option value="">Toutes actions</option>
                                    @foreach (['creation' => 'Création', 'modification' => 'Modification', 'suppression' => 'Suppression', 'restauration' => 'Restauration'] as $valeur => $libelle)
                                        <option value="{{ $valeur }}" @selected(request('action') === $valeur)>{{ $libelle }}</option>
                                    @endforeach
                                </select>
                                <select class="form-select" name="entity_type" onchange="this.form.submit()">
                                    <option value="">Tous objets</option>
                                    @foreach ($typesEntites as $type)
                                        <option value="{{ $type }}" @selected(request('entity_type') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <input type="date" class="form-control" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date/heure</th>
                                        <th>Utilisateur</th>
                                        <th>Action</th>
                                        <th>Objet</th>
                                        <th>Motif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($entrees as $entree)
                                        @php
                                            $classes = ['creation' => 'success', 'modification' => 'warning', 'suppression' => 'danger', 'restauration' => 'info'];
                                        @endphp
                                        <tr>
                                            <td>{{ $entree->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if ($entree->utilisateur)
                                                    {{ $entree->utilisateur->name }}
                                                    <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $entree->utilisateur->departement->nom ?? '—' }}</span>
                                                @else
                                                    Système
                                                @endif
                                            </td>
                                            <td><span class="badge bg-{{ $classes[$entree->action] ?? 'secondary' }}-subtle text-{{ $classes[$entree->action] ?? 'secondary' }} text-capitalize">{{ $entree->action }}</span></td>
                                            <td>{{ $entree->entity_type }} #{{ $entree->entity_id }}</td>
                                            <td>{{ $entree->motifAffiche() }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucune activité enregistrée pour ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
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
