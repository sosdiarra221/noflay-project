@extends('partials.layouts.master-administration')

@section('title', 'Nouvel utilisateur | Direction & Administration')
@section('title-sub', 'Direction & Administration')
@section('pagetitle', 'Nouvel utilisateur')

@section('content')
    <div id="layout-wrapper">

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

        <form action="{{ route('administration.utilisateurs.store') }}" method="POST">
            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Nouvel utilisateur</h6></div>
                @include('administration.utilisateurs._formulaire', ['utilisateur' => null])
            </div>
        </form>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
