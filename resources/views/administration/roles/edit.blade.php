@extends('partials.layouts.master-administration')

@section('title', 'Permissions — '.$role->libelle.' | Direction & Administration')
@section('title-sub', 'Direction & Administration')
@section('pagetitle', 'Permissions — '.$role->libelle)

@section('content')
    <div id="layout-wrapper">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('administration.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Rôle</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Libellé</label>
                            <input type="text" class="form-control" name="libelle" value="{{ old('libelle', $role->libelle) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Permissions accordées à ce rôle</h6></div>
                <div class="card-body">
                    @foreach ($permissionsParModule as $module => $permissions)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <h6 class="mb-0">{{ $module ?? 'Autres' }}</h6>
                                <div class="hstack gap-2">
                                    <button type="button" class="btn btn-light-primary btn-sm bouton-tout-cocher" data-module="{{ $module }}">Tout cocher</button>
                                    <button type="button" class="btn btn-light-secondary btn-sm bouton-tout-decocher" data-module="{{ $module }}">Tout décocher</button>
                                </div>
                            </div>
                            <div class="row g-2" data-groupe-module="{{ $module }}">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm{{ $permission->id }}" @checked(in_array($permission->id, $permissionsActives))>
                                            <label class="form-check-label" for="perm{{ $permission->id }}">{{ $permission->libelle }}</label>
                                            <div class="fs-11 text-muted">{{ $permission->cle }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('administration.roles.index') }}" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les permissions</button>
                </div>
            </div>
        </form>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.bouton-tout-cocher').forEach(function (bouton) {
                bouton.addEventListener('click', function () {
                    document.querySelector(`[data-groupe-module="${bouton.dataset.module}"]`)
                        .querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = true);
                });
            });
            document.querySelectorAll('.bouton-tout-decocher').forEach(function (bouton) {
                bouton.addEventListener('click', function () {
                    document.querySelector(`[data-groupe-module="${bouton.dataset.module}"]`)
                        .querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
                });
            });
        });
    </script>
@endsection
