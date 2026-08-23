@extends('partials.layouts.master-commercial')

@section('title', $titre.' | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', $titre)

@section('content')
    <div id="layout-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-10">
                        <i class="bi bi-hourglass-split fs-1 text-muted mb-3 d-block"></i>
                        <h5>{{ $titre }}</h5>
                        <p class="text-muted mb-0">Cette fonctionnalité arrive dans une prochaine phase du module Commercial.</p>
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
