@extends('partials.layouts.master_auth')

@section('title', 'Session verrouillée | Takha CRM')

@section('content')

    <div>
        <img src="assets/images/auth/login_bg.jpg" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="assets/images/auth/auth_bg_dark.jpg" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8 text-center">
                            <img src="{{ $utilisateur->avatarUrl() }}" alt="Avatar" class="rounded-circle mb-3" width="72" height="72">
                            <h6 class="mb-1 fw-semibold">{{ $utilisateur->name }}</h6>
                            <p class="text-muted fs-13 mb-6">Session verrouillée pour inactivité. Entrez votre code PIN pour continuer.</p>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('verrouillage.store') }}" method="POST">
                                @csrf
                                @include('partials.pin-pad', ['champNom' => 'code_pin'])
                                <button type="submit" class="btn btn-primary w-full mt-4 mb-3">Déverrouiller<i
                                        class="bi bi-unlock ms-1 fs-16"></i></button>
                            </form>
                            <a href="{{ route('login') }}" class="fs-13">Se reconnecter avec un autre compte</a>
                        </div>
                    </div>
                    <p class="position-relative text-center fs-12 mb-0">© 2025 Takha CRM. Conception par Pene Technologies Service (PTS)</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
