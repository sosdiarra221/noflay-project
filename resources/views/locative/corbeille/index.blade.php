@extends('partials.layouts.master-locative')

@section('title', 'Corbeille | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Corbeille')

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
                        <h6 class="card-title mb-0 fw-semibold">
                            Corbeille <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $elements->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Élément</th>
                                        <th>Supprimé le</th>
                                        <th>Par</th>
                                        <th>Motif</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($elements as $element)
                                        <tr>
                                            <td><span class="badge bg-light-subtle text-body">{{ $element->libelle_type }}</span></td>
                                            <td class="fw-medium">{{ $element->libelle }}</td>
                                            <td>{{ $element->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $element->supprime_par ?? '—' }}</td>
                                            <td>{{ $element->motif_suppression ?? '—' }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <form action="{{ route('locative.corbeille.restaurer', [$element->type, $element->id]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-light-success btn-sm">
                                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurer
                                                        </button>
                                                    </form>
                                                    @can('locative.suppression-definitive')
                                                        <form action="{{ route('locative.corbeille.supprimer-definitivement', [$element->type, $element->id]) }}" method="POST"
                                                            onsubmit="return confirm('Suppression définitive et irréversible. Continuer ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-light-danger btn-sm">
                                                                <i class="bi bi-trash3 me-1"></i>Définitif
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">La corbeille est vide.</td></tr>
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
