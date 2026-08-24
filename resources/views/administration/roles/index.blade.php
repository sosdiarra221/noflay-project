@extends('partials.layouts.master-administration')

@section('title', 'Rôles & permissions | Direction & Administration')
@section('title-sub', 'Direction & Administration')
@section('pagetitle', 'Rôles & permissions')

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
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <h6 class="mb-0">Rôles <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $roles->count() }}</span></h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                            <i class="bi bi-plus-lg me-1"></i>Nouveau rôle
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Rôle</th>
                                        <th>Utilisateurs</th>
                                        <th>Permissions accordées</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $role)
                                        <tr>
                                            <td class="fw-medium">
                                                {{ $role->libelle }}
                                                @if ($role->estSysteme())
                                                    <span class="badge bg-light-subtle text-body ms-1">Système</span>
                                                @endif
                                            </td>
                                            <td>{{ $role->utilisateurs_count }}</td>
                                            <td>{{ $role->permissions_count }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('administration.roles.edit', $role) }}" class="btn btn-light-primary btn-sm"><i class="bi bi-shield-check me-1"></i>Permissions</a>
                                                    @if (! $role->estSysteme() && $role->utilisateurs_count === 0)
                                                        <form action="{{ route('administration.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Supprimer ce rôle ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-5">Aucun rôle.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('administration.roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau rôle</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Libellé du rôle</label>
                        <input type="text" class="form-control" name="libelle" placeholder="Ex: Superviseur commercial" required>
                        <p class="text-muted fs-12 mt-2 mb-0">Les permissions se configurent ensuite via le bouton « Permissions ».</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
