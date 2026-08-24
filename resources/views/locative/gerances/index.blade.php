@extends('partials.layouts.master-locative')

@section('title', 'Gérances | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Contrats de gérance')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_gerances">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_gerances_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_gerances_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_gerances">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-4">
                                        <select class="form-select" name="bailleur_id">
                                            <option value="">Tous les bailleurs</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="type_gerance">
                                            <option value="">Tous types</option>
                                            <option value="gestion_locative" @selected(request('type_gerance') === 'gestion_locative')>Gestion locative</option>
                                            <option value="gestion_vente" @selected(request('type_gerance') === 'gestion_vente')>Gestion vente</option>
                                            <option value="gestion_locative_vente" @selected(request('type_gerance') === 'gestion_locative_vente')>Gestion locative + vente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="statut">
                                            <option value="">Tous statuts</option>
                                            @foreach (['brouillon', 'en_attente_signature', 'actif', 'suspendu', 'expire', 'resilie', 'archive'] as $statutOption)
                                                <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button class="btn btn-light-primary" type="submit"><i class="ri-equalizer-line me-2"></i>Filtrer</button>
                                        <a href="{{ route('locative.gerances.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Gérances <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $gerances->count() }}</span></h6>
                        <a href="{{ route('locative.gerances.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Nouvelle gérance
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Numéro</th>
                                        <th scope="col">Bailleur</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Période</th>
                                        <th scope="col">Biens</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($gerances as $gerance)
                                        <tr>
                                            <td><a href="{{ route('locative.gerances.show', $gerance) }}" class="fw-medium text-reset">{{ $gerance->numero }}</a></td>
                                            <td>{{ $gerance->bailleur->nom_complet }}</td>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $gerance->type_gerance) }}</td>
                                            <td>{{ $gerance->date_debut->format('d/m/Y') }} @if ($gerance->date_fin) → {{ $gerance->date_fin->format('d/m/Y') }} @endif</td>
                                            <td>{{ $gerance->biens_count }}</td>
                                            <td>
                                                @php
                                                    $classes = ['actif' => 'success', 'brouillon' => 'secondary', 'en_attente_signature' => 'warning', 'suspendu' => 'warning', 'expire' => 'dark', 'resilie' => 'danger', 'archive' => 'dark'];
                                                @endphp
                                                <span class="badge bg-{{ $classes[$gerance->statut] ?? 'secondary' }}-subtle text-{{ $classes[$gerance->statut] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $gerance->statut) }}</span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('locative.gerances.show', $gerance) }}" class="btn btn-light-success btn-sm" title="Consulter le dossier">
                                                        <i class="bi bi-folder2-open me-1"></i>Dossier
                                                    </a>
                                                    <button type="button" class="btn btn-light-info btn-sm" data-bs-toggle="modal" data-bs-target="#mandatGeranceModal{{ $gerance->id }}" title="Consulter le contrat">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i>Contrat
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">Aucun contrat de gérance ne correspond à ces filtres.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($gerances as $gerance)
            {{-- Visualiseur du Mandat de gérance --}}
            <div class="modal fade" id="mandatGeranceModal{{ $gerance->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Mandat de gérance — {{ $gerance->numero }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body p-0">
                            <iframe src="{{ route('locative.gerances.apercu', $gerance) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('locative.gerances.pdf', $gerance) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
