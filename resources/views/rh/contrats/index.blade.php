@extends('partials.layouts.master-rh')

@section('title', 'Contrats de travail | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Contrats de travail')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

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
                        <label class="form-label">Employé</label>
                        <select class="form-select" name="employe_id" id="selectFiltreEmploye" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            @foreach ($employes as $employe)
                                <option value="{{ $employe->id }}" @selected(request('employe_id') == $employe->id)>{{ $employe->nom_complet }} ({{ $employe->matricule }})</option>
                            @endforeach
                        </select>
                    </div>
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
                                    <td>
                                        <div class="hstack gap-2">
                                            <a href="{{ route('rh.employes.show', $contrat->employe) }}" class="btn btn-light-success btn-sm">Voir l'employé</a>
                                            <button type="button" class="btn btn-light-info btn-sm" title="Contrat en PDF" onclick="ouvrirApercuContratPdf({{ Js::from(route('rh.contrats.pdf.apercu', $contrat)) }}, {{ Js::from($contrat->numero) }}, {{ Js::from(route('rh.contrats.pdf', $contrat)) }})"><i class="bi bi-file-earmark-pdf"></i></button>
                                        </div>
                                    </td>
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

    {{-- Aperçu PDF du contrat --}}
    <div class="modal fade" id="apercuContratPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="apercuContratPdfTitre">Contrat</h5>
                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="apercuContratPdfFrame" src="" style="width: 100%; height: 70vh; border: 0;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                    <a id="apercuContratPdfTelecharger" href="#" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger</a>
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
            new Choices(document.getElementById('selectFiltreEmploye'), { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher un employé...' });
        });

        function ouvrirApercuContratPdf(urlApercu, numero, urlTelecharger) {
            document.getElementById('apercuContratPdfTitre').textContent = 'Contrat — ' + numero;
            document.getElementById('apercuContratPdfFrame').src = urlApercu;
            document.getElementById('apercuContratPdfTelecharger').href = urlTelecharger;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('apercuContratPdfModal')).show();
        }
    </script>
@endsection
