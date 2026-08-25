@extends('partials.layouts.master-rh')

@section('title', 'Contrats de travail | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Contrats de travail')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">État</label>
                        <select class="form-select" name="etat" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="actif" @selected(request('etat') === 'actif')>Actif</option>
                            <option value="cloture" @selected(request('etat') === 'cloture')>Clôturé</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Échéance</label>
                        <select class="form-select" name="echeance" onchange="this.form.submit()">
                            <option value="">Toutes</option>
                            <option value="7" @selected(request('echeance') === '7')>Sous 7 jours</option>
                            <option value="30" @selected(request('echeance') === '30')>Sous 30 jours</option>
                            <option value="90" @selected(request('echeance') === '90')>Sous 90 jours</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('rh.contrats.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Contrats <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $contrats->count() }}</span></h6></div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Numéro</th><th>Employé</th><th>Département</th><th>Type</th><th>Début</th><th>Fin prévue</th><th>Montant</th><th>État</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($contrats as $contrat)
                                <tr>
                                    <td class="fw-medium">{{ $contrat->numero }}</td>
                                    <td>{{ $contrat->employe->nom_complet }}</td>
                                    <td>{{ $contrat->employe->departement->nom ?? '—' }}</td>
                                    <td>{{ $contrat->libelleType() }}</td>
                                    <td>{{ $contrat->date_debut->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($contrat->date_prevu_fin)
                                            {{ $contrat->date_prevu_fin->format('d/m/Y') }}
                                            @if ($contrat->etat === 'actif' && $contrat->joursAvantEcheance() !== null && $contrat->joursAvantEcheance() <= 30)
                                                <span class="badge bg-{{ $contrat->joursAvantEcheance() <= 7 ? 'danger' : 'warning' }}-subtle text-{{ $contrat->joursAvantEcheance() <= 7 ? 'danger' : 'warning' }} ms-1">{{ $contrat->joursAvantEcheance() }} j</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $contrat->montant ? number_format($contrat->montant, 0, ',', ' ').' FCFA' : '—' }}</td>
                                    <td>
                                        @if ($contrat->etat === 'actif')
                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Clôturé</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('rh.employes.show', $contrat->employe) }}" class="btn btn-light-success btn-sm">Voir l'employé</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-5">Aucun contrat ne correspond à ces filtres.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
