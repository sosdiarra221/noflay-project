@extends('central.layout')

@section('title', 'Sociétés | Espace éditeur')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Sociétés (PME)</h4>
        <a href="{{ route('central.societes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle société
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Société</th>
                        <th>Sous-domaine</th>
                        <th>Plan</th>
                        <th>Modules actifs</th>
                        <th>Statut</th>
                        <th>Créée le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($societes as $tenant)
                        <tr>
                            <td class="fw-medium">{{ $tenant->nom_pme ?? $tenant->id }}</td>
                            <td>
                                @if ($tenant->domains->first())
                                    <a href="http://{{ $tenant->domains->first()->domain }}" target="_blank">{{ $tenant->domains->first()->domain }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $tenant->plan ?? '—' }}</td>
                            <td>{{ $tenant->nb_modules_actifs }} / {{ count(config('modules')) - 1 }}</td>
                            <td>
                                @if ($tenant->statut === 'actif')
                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Suspendu</span>
                                @endif
                            </td>
                            <td>{{ $tenant->created_at?->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('central.societes.show', $tenant) }}" class="btn btn-sm btn-outline-secondary">Détail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucune société pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
