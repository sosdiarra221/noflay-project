@extends('partials.layouts.master-locative')

@section('title', 'Échéances | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Échéances & Loyers')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form method="GET" class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Échéances <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $echeances->count() }}</span>
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <select class="form-select" name="statut" onchange="this.form.submit()">
                                    <option value="">Tous statuts</option>
                                    @foreach (['a_venir', 'echu', 'partiellement_paye', 'paye', 'en_retard', 'annule'] as $statutOption)
                                        <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                    @endforeach
                                </select>
                                <select class="form-select" name="annee" onchange="this.form.submit()">
                                    <option value="">Toutes années</option>
                                    @for ($annee = now()->year - 1; $annee <= now()->year + 1; $annee++)
                                        <option value="{{ $annee }}" @selected((string) request('annee') === (string) $annee)>{{ $annee }}</option>
                                    @endfor
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Période</th>
                                        <th>Locataire</th>
                                        <th>Bien</th>
                                        <th>Attendu</th>
                                        <th>Payé</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($echeances as $echeance)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y') }}</td>
                                            <td>{{ $echeance->contratLocation->location->locataire->nom_complet }}</td>
                                            <td>{{ $echeance->contratLocation->bien->titre }}</td>
                                            <td>{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @php
                                                    $classesE = ['paye' => 'success', 'partiellement_paye' => 'warning', 'en_retard' => 'danger', 'a_venir' => 'secondary', 'annule' => 'dark'];
                                                @endphp
                                                <span class="badge bg-{{ $classesE[$echeance->statut] ?? 'secondary' }}-subtle text-{{ $classesE[$echeance->statut] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $echeance->statut) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('locative.contrats.show', $echeance->contratLocation) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-5">Aucune échéance ne correspond à ces filtres.</td></tr>
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
