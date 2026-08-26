@extends('partials.layouts.master-rh')

@section('title', $employe->nom_complet.' | RH')
@section('title-sub', 'Employés')
@section('pagetitle', $employe->nom_complet)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="mt-2">
                <div class="card-body p-5">
                    <div class="d-flex flex-wrap align-items-start gap-5">
                        <div class="flex-shrink-0">
                            <img src="{{ $employe->photo ? asset('storage/'.$employe->photo) : asset('assets/images/avatar/avatar-10.jpg') }}" class="rounded-circle border border-4 border-white shadow-lg" width="110" height="110" style="object-fit: cover;">
                        </div>
                        <div class="flex-shrink-0 text-center bg-info-subtle rounded p-3" style="width: 110px;">
                            <div class="text-info fs-24 fw-bold lh-1">{{ $employe->solde_conges_formate }}</div>
                            <div class="text-info fs-11 fw-medium mt-1">j. de congé</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <h5 class="mb-1">{{ $employe->nom_complet }}
                                    @if ($employe->statut === 'actif')
                                        <span class="badge bg-success-subtle text-success ms-1">Actif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary ms-1">Sortie</span>
                                    @endif
                                </h5>
                                <p class="text-muted fs-12 mb-0">{{ $employe->matricule }} — {{ $employe->poste->nom ?? '—' }}</p>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2"><i class="bi bi-diagram-3 me-1"></i>{{ $employe->departement->nom ?? '—' }}</span>
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2"><i class="bi bi-geo-alt me-1"></i>{{ $employe->sites->pluck('nom')->implode(', ') ?: 'Aucun site' }}</span>
                            </div>
                        </div>
                        @can('rh.gerer')
                            <div class="flex-shrink-0 d-flex flex-wrap gap-2">
                                <a href="{{ route('rh.employes.edit', $employe) }}" class="btn btn-light-primary"><i class="bi bi-pencil-square me-1"></i>Modifier</a>
                                <button type="button" class="btn btn-light-info" data-bs-toggle="modal" data-bs-target="#ajusterSoldeModal"><i class="bi bi-calendar-plus me-1"></i>Ajuster le solde</button>
                                <button type="button" class="btn btn-light-success" data-bs-toggle="modal" data-bs-target="#nouveauContratModal"><i class="bi bi-file-earmark-plus me-1"></i>Nouveau contrat</button>
                                @if ($employe->statut === 'actif')
                                    <button type="button" class="btn btn-light-danger" data-bs-toggle="modal" data-bs-target="#archiverModal"><i class="bi bi-box-arrow-right me-1"></i>Archiver</button>
                                @else
                                    <form action="{{ route('rh.employes.reactiver', $employe) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-light-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Réactiver</button>
                                    </form>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#infos-pane" type="button">Vue générale</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#contrats-pane" type="button">Contrats <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->contrats->count() }}</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#affectations-pane" type="button">Affectations <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->affectations->count() }}</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#conges-pane" type="button">Congés <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->absences->count() }}</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents-pane" type="button">Documents <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->documents->count() }}</span></button></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="infos-pane">
                        @include('rh.employes.partials._infos-generales')
                    </div>

                    <div class="tab-pane fade" id="contrats-pane">
                        @include('rh.employes.partials._contrats')
                    </div>

                    <div class="tab-pane fade" id="affectations-pane">
                        @include('rh.employes.partials._affectations')
                    </div>

                    <div class="tab-pane fade" id="conges-pane">
                        @include('rh.employes.partials._conges')
                    </div>

                    <div class="tab-pane fade" id="documents-pane">
                        @include('rh.employes.partials._documents')
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('rh.employes.partials._modals')

    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        function ouvrirApercuDocument(urlApercu, titre, previsualisable, urlTelecharger) {
            document.getElementById('apercuDocumentTitre').textContent = titre;
            document.getElementById('apercuDocumentTelecharger').href = urlTelecharger;

            const iframe = document.getElementById('apercuDocumentFrame');
            const indisponible = document.getElementById('apercuDocumentIndisponible');
            if (previsualisable) {
                iframe.src = urlApercu;
                iframe.classList.remove('d-none');
                indisponible.classList.add('d-none');
            } else {
                iframe.src = '';
                iframe.classList.add('d-none');
                indisponible.classList.remove('d-none');
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('apercuDocumentModal')).show();
        }
    </script>
@endsection
