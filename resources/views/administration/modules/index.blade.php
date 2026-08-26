@extends('partials.layouts.master-administration')

@section('title', 'Modules | Administration')
@section('title-sub', 'Administration')
@section('pagetitle', 'Modules')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Modules souscrits</h6>
                <p class="text-muted fs-12 mb-0">Aperçu des modules disponibles pour votre société. L'activation ou la désactivation d'un module dépend de votre abonnement et est gérée par l'éditeur du logiciel — contactez-le pour toute modification.</p>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach ($modules as $module)
                        <div class="col-md-6 col-xl-4">
                            <div class="card border h-100 {{ $module->actif ? '' : 'opacity-75' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-shrink-0 h-50px w-50px bg-primary-subtle d-flex align-items-center justify-content-center rounded text-primary fs-3">
                                            <i class="{{ $module->icone }}"></i>
                                        </div>
                                        @if ($module->cle === 'administration')
                                            <span class="badge bg-secondary-subtle text-secondary">Toujours actif</span>
                                        @endif
                                    </div>
                                    <h6 class="mb-1">{{ $module->nom }}</h6>
                                    <p class="text-muted fs-13 mb-2">{{ $module->description }}</p>
                                    @if ($module->actif)
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Actif</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle me-1"></i>Non souscrit</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
