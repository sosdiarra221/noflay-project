@extends('partials.layouts.master-finance')

@section('title', 'Historique des reversements | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Historique des reversements')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_historique">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_historique_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_historique_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_historique">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4" id="formFiltresHistorique">
                                    <div class="col-md-6">
                                        <select class="form-select" name="bailleur_id" id="filtreBailleurHistoriqueSelect">
                                            <option value="">Tous les bailleurs</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" name="statut" onchange="this.form.submit()">
                                            <option value="">Tous statuts</option>
                                            <option value="a_verser" @selected(request('statut') === 'a_verser')>À verser</option>
                                            <option value="verse" @selected(request('statut') === 'verse')>Versé</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('finance.reversements.historique') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                                <p class="text-muted fs-12 mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Les filtres s'appliquent automatiquement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Reversements <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $reversements->count() }}</span></h6>
                        <a href="{{ route('finance.reversements.index') }}" class="btn btn-light-primary btn-sm">Retour à la période courante</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Bailleur</th>
                                        <th>Période</th>
                                        <th>Montant net</th>
                                        <th>Statut</th>
                                        <th>Date versement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reversements as $reversement)
                                        <tr>
                                            <td class="fw-medium">{{ $reversement->numero }}</td>
                                            <td>{{ $reversement->bailleur->nom_complet }}</td>
                                            <td>{{ ucfirst(\Carbon\Carbon::createFromDate($reversement->periode_annee, $reversement->periode_mois, 1)->translatedFormat('F Y')) }}</td>
                                            <td>{{ number_format($reversement->montant_net, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @if ($reversement->statut === 'verse')
                                                    <span class="badge bg-success-subtle text-success">Versé</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">À verser</span>
                                                @endif
                                            </td>
                                            <td>{{ $reversement->date_versement?->format('d/m/Y') ?: '—' }}</td>
                                            <td>
                                                @if ($reversement->statut === 'verse')
                                                    <a href="{{ route('finance.reversements.bordereau', $reversement) }}" class="btn btn-light-info icon-btn-sm" target="_blank"><i class="bi bi-receipt"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-5">Aucun reversement ne correspond à ces filtres.</td></tr>
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
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formFiltresHistorique');
            new Choices(document.getElementById('filtreBailleurHistoriqueSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un bailleur...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreBailleurHistoriqueSelect').addEventListener('change', () => form.submit());
        });
    </script>
@endsection
