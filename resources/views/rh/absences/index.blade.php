@extends('partials.layouts.master-rh')

@section('title', 'Congés & Absences | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Congés & Absences')

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

        <div class="row g-3 mb-1">
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-warning text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-hourglass-split"></i></div>
                            <div><h5 class="mb-2">{{ $stats['en_attente'] }}</h5><p class="text-muted mb-0 fs-12">Demandes en attente</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-success text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-check-circle"></i></div>
                            <div><h5 class="mb-2">{{ $stats['validees'] }}</h5><p class="text-muted mb-0 fs-12">Demandes validées</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-info text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-person-walking"></i></div>
                            <div><h5 class="mb-2">{{ $stats['en_cours'] }}</h5><p class="text-muted mb-0 fs-12">Employés actuellement absents</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Employé</label>
                        <select class="form-select" name="employe_id" id="selectFiltreEmployeAbsence" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            @foreach ($employes as $employe)
                                <option value="{{ $employe->id }}" @selected(request('employe_id') == $employe->id)>{{ $employe->nom_complet }} ({{ $employe->matricule }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type_absence_id" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            @foreach ($typesAbsence as $type)
                                <option value="{{ $type->id }}" @selected(request('type_absence_id') == $type->id)>{{ $type->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="statut" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            @foreach (\App\Models\Rh\Absence::STATUTS as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('rh.absences.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Demandes <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $absences->count() }}</span></h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nouvelleAbsenceModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouvelle demande
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Numéro</th><th>Employé</th><th>Type</th><th>Début</th><th>Retour</th><th>Jours</th><th>Statut</th><th>Justificatif</th><th></th></tr></thead>
                        <tbody>
                            @php $classesStatut = ['en_attente' => 'warning', 'validee' => 'success', 'refusee' => 'danger', 'annulee' => 'secondary']; @endphp
                            @forelse ($absences as $absence)
                                <tr>
                                    <td class="fw-medium">{{ $absence->numero }}</td>
                                    <td>{{ $absence->employe->nom_complet ?? '—' }}</td>
                                    <td>{{ $absence->typeAbsence->nom ?? '—' }}</td>
                                    <td>{{ $absence->date_debut->format('d/m/Y') }}</td>
                                    <td>{{ $absence->date_retour->format('d/m/Y') }}</td>
                                    <td>{{ rtrim(rtrim(number_format($absence->nombre_jours, 2, ',', ' '), '0'), ',') }}</td>
                                    <td><span class="badge bg-{{ $classesStatut[$absence->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$absence->statut] ?? 'secondary' }}">{{ $absence->libelleStatut() }}</span></td>
                                    <td>
                                        @if ($absence->document)
                                            <button type="button" class="btn btn-light-info icon-btn-sm" title="Voir le justificatif" onclick="ouvrirApercuDocumentAbsence({{ Js::from(route('rh.absences.document.apercu', $absence)) }}, {{ Js::from($absence->numero) }})"><i class="bi bi-file-earmark-text"></i></button>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @can('rh.gerer')
                                            <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statutAbsenceModal{{ $absence->id }}"><i class="bi bi-arrow-repeat me-1"></i>Statut</button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-5">Aucune demande ne correspond à ces filtres.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Nouvelle demande d'absence --}}
    <div class="modal fade" id="nouvelleAbsenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.absences.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle demande d'absence</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="mb-3">
                                    <label class="form-label">Employé<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select select-employe-absence" name="employe_id" id="selectEmployeAbsence" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach ($employes as $employe)
                                            <option value="{{ $employe->id }}">{{ $employe->nom_complet }} ({{ $employe->matricule }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Type d'absence<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select select-type-absence" name="type_absence_id" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach ($typesAbsence as $type)
                                            <option value="{{ $type->id }}">{{ $type->nom }}{{ $type->est_urgence ? ' (dispensé du préavis de 3 jours)' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Date de début<span class="text-danger ms-1">*</span></label>
                                        <input type="date" class="form-control" name="date_debut" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date de retour<span class="text-danger ms-1">*</span></label>
                                        <input type="date" class="form-control" name="date_retour" required>
                                    </div>
                                </div>
                                <p class="text-muted fs-11">Le nombre de jours décomptés exclut automatiquement les jours fériés compris dans la période. Sauf type « urgence », la demande doit être faite au moins 3 jours avant la date de début.</p>
                                <div class="mb-3">
                                    <label class="form-label">Motif</label>
                                    <textarea class="form-control" name="motif" rows="2"></textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Document justificatif</label>
                                    <input type="file" class="form-control" name="document">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <label class="form-label">Employé sélectionné</label>
                                <div id="apercuEmployeAbsence" class="bg-light-subtle rounded p-3">
                                    <p class="text-muted fs-12 text-center py-5 mb-0">Sélectionnez un employé pour voir son solde de congé et ses informations ici.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer la demande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @can('rh.gerer')
        @foreach ($absences as $absence)
            <div class="modal fade" id="statutAbsenceModal{{ $absence->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('rh.absences.statut', $absence) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Statut de la demande {{ $absence->numero }}</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted fs-12">{{ $absence->employe->nom_complet ?? '—' }} — {{ $absence->typeAbsence->nom ?? '—' }} — {{ $absence->date_debut->format('d/m/Y') }} au {{ $absence->date_retour->format('d/m/Y') }} ({{ rtrim(rtrim(number_format($absence->nombre_jours, 2, ',', ' '), '0'), ',') }} j)</p>
                                <div class="alert alert-info fs-12">Passer au statut « Validée » déduit automatiquement les jours du solde de congé de l'employé. Un retour en arrière depuis « Validée » les recrédite.</div>
                                <div class="mb-3">
                                    <label class="form-label">Statut<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="statut" required>
                                        @foreach (\App\Models\Rh\Absence::STATUTS as $valeur => $libelle)
                                            <option value="{{ $valeur }}" @selected($absence->statut === $valeur)>{{ $libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Commentaire</label>
                                    <input type="text" class="form-control" name="commentaire_statut" value="{{ $absence->commentaire_statut }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endcan

    {{-- Aperçu du justificatif --}}
    <div class="modal fade" id="apercuDocumentAbsenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="apercuDocumentAbsenceTitre">Justificatif</h5>
                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="apercuDocumentAbsenceFrame" src="" style="width: 100%; height: 70vh; border: 0;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
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
        function ouvrirApercuDocumentAbsence(url, numero) {
            document.getElementById('apercuDocumentAbsenceTitre').textContent = 'Justificatif — ' + numero;
            document.getElementById('apercuDocumentAbsenceFrame').src = url;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('apercuDocumentAbsenceModal')).show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            new Choices(document.getElementById('selectFiltreEmployeAbsence'), { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher un employé...' });
            new Choices(document.querySelector('.select-type-absence'), { searchEnabled: true, itemSelectText: '' });

            const employesData = {!! json_encode($employes->map(fn ($e) => [
                'id' => (string) $e->id,
                'nom' => $e->nom_complet,
                'matricule' => $e->matricule,
                'poste' => $e->poste->nom ?? '—',
                'departement' => $e->departement->nom ?? '—',
                'solde' => rtrim(rtrim(number_format((float) $e->solde_conges, 2, ',', ' '), '0'), ','),
            ])) !!};

            const selectEmploye = document.getElementById('selectEmployeAbsence');
            const apercu = document.getElementById('apercuEmployeAbsence');

            function actualiserApercuEmploye() {
                const e = employesData.find(function (x) { return x.id === selectEmploye.value; });
                if (!e) {
                    apercu.innerHTML = '<p class="text-muted fs-12 text-center py-5 mb-0">Sélectionnez un employé pour voir son solde de congé et ses informations ici.</p>';
                    return;
                }
                apercu.innerHTML = '<div class="text-center mb-3">'
                    + '<div class="fw-semibold fs-16">' + e.nom + '</div>'
                    + '<div class="text-muted fs-12">' + e.matricule + ' — ' + e.poste + '</div>'
                    + '<div class="text-muted fs-12">' + e.departement + '</div>'
                    + '</div>'
                    + '<div class="text-center p-3 bg-white rounded border">'
                    + '<div class="fs-24 fw-bold text-primary">' + e.solde + '</div>'
                    + '<div class="text-muted fs-12">jour(s) de solde de congé disponibles</div>'
                    + '</div>';
            }

            new Choices(selectEmploye, { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher un employé...' });
            selectEmploye.addEventListener('change', actualiserApercuEmploye);
        });
    </script>
@endsection
