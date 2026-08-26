@extends('partials.layouts.master-central')

@section('title', 'Réglages | Espace éditeur')
@section('title-sub', 'Espace éditeur')
@section('pagetitle', 'Réglages')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <ul class="nav nav-pills mb-4" id="reglagesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-tab-pane" type="button" role="tab">Général</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#smtp-tab-pane" type="button" role="tab">Configuration SMTP</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations-tab-pane" type="button" role="tab">Intégrations</button>
            </li>
        </ul>

        <div class="tab-content" id="reglagesTabContent">

            {{-- Onglet Général --}}
            <div class="tab-pane fade show active" id="general-tab-pane" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Identité du logiciel</h6>
                        <p class="text-muted fs-12 mb-0">Logo et icône utilisés dans l'espace éditeur (tableau de bord, sociétés, packages). N'affecte pas l'interface des sociétés clientes.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('central.reglages.update-general') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <label for="nom_application" class="form-label">Nom de l'application <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nom_application" name="nom_application" value="{{ old('nom_application', $reglage->nom_application) }}" required>
                                </div>
                                <div class="col-lg-4">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                    @if ($reglage->logo)
                                        <img src="{{ asset($reglage->logo) }}" alt="Logo actuel" class="mt-2" height="30">
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label for="favicon" class="form-label">Icône (favicon)</label>
                                    <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                                    @if ($reglage->favicon)
                                        <img src="{{ asset($reglage->favicon) }}" alt="Favicon actuel" class="mt-2" height="24">
                                    @endif
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Onglet SMTP --}}
            <div class="tab-pane fade" id="smtp-tab-pane" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Configuration SMTP du logiciel</h6>
                        <p class="text-muted fs-12 mb-0">Utilisée pour les emails envoyés par l'espace éditeur — distincte du SMTP de chaque société.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('central.reglages.update-smtp') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <label for="smtp_type" class="form-label">Type d'envoi <span class="text-danger">*</span></label>
                                    <select class="form-select" id="smtp_type" name="smtp_type" required>
                                        @foreach (['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'resend' => 'Resend', 'log' => 'Journal (log, pour tests)'] as $valeur => $libelle)
                                            <option value="{{ $valeur }}" @selected(old('smtp_type', $reglage->smtp_type) == $valeur)>{{ $libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label for="smtp_from_name" class="form-label">Nom de l'expéditeur</label>
                                    <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" value="{{ old('smtp_from_name', $reglage->smtp_from_name) }}">
                                </div>
                                <div class="col-lg-4">
                                    <label for="smtp_from_address" class="form-label">Email expéditeur</label>
                                    <input type="email" class="form-control" id="smtp_from_address" name="smtp_from_address" value="{{ old('smtp_from_address', $reglage->smtp_from_address) }}">
                                </div>
                                <div class="col-lg-4">
                                    <label for="smtp_host" class="form-label">Hôte SMTP</label>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="{{ old('smtp_host', $reglage->smtp_host) }}" placeholder="smtp.gmail.com">
                                </div>
                                <div class="col-lg-4">
                                    <label for="smtp_port" class="form-label">Port</label>
                                    <input type="text" class="form-control" id="smtp_port" name="smtp_port" value="{{ old('smtp_port', $reglage->smtp_port) }}" placeholder="587">
                                </div>
                                <div class="col-lg-4">
                                    <label for="smtp_encryption" class="form-label">Sécurité</label>
                                    <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                        <option value="">Aucune</option>
                                        <option value="tls" @selected(old('smtp_encryption', $reglage->smtp_encryption) == 'tls')>TLS</option>
                                        <option value="ssl" @selected(old('smtp_encryption', $reglage->smtp_encryption) == 'ssl')>SSL</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label for="smtp_username" class="form-label">Utilisateur</label>
                                    <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="{{ old('smtp_username', $reglage->smtp_username) }}">
                                </div>
                                <div class="col-lg-4">
                                    <label for="smtp_password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="••••••••" autocomplete="new-password">
                                    <small class="text-muted">Laisser vide pour conserver le mot de passe actuel.</small>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Onglet Intégrations --}}
            <div class="tab-pane fade" id="integrations-tab-pane" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Intégrations</h6>
                        <p class="text-muted fs-12 mb-0">Clés Pusher et Firebase du logiciel.</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('central.reglages.update-integrations') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <h6 class="mb-3">Pusher</h6>
                            <div class="row g-4 mb-4">
                                <div class="col-lg-3">
                                    <label class="form-label">App ID</label>
                                    <input type="text" class="form-control" name="pusher_app_id" value="{{ old('pusher_app_id', $reglage->pusher_app_id) }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Key</label>
                                    <input type="text" class="form-control" name="pusher_key" value="{{ old('pusher_key', $reglage->pusher_key) }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Secret</label>
                                    <input type="password" class="form-control" name="pusher_secret" placeholder="••••••••" autocomplete="new-password">
                                    <small class="text-muted">Laisser vide pour conserver le secret actuel.</small>
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Cluster</label>
                                    <input type="text" class="form-control" name="pusher_cluster" value="{{ old('pusher_cluster', $reglage->pusher_cluster) }}" placeholder="eu">
                                </div>
                            </div>
                            <h6 class="mb-3">Firebase</h6>
                            <div class="row g-4">
                                <div class="col-lg-3">
                                    <label class="form-label">API Key</label>
                                    <input type="text" class="form-control" name="firebase_api_key" value="{{ old('firebase_api_key', $reglage->firebase_api_key) }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Project ID</label>
                                    <input type="text" class="form-control" name="firebase_project_id" value="{{ old('firebase_project_id', $reglage->firebase_project_id) }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Messaging Sender ID</label>
                                    <input type="text" class="form-control" name="firebase_messaging_sender_id" value="{{ old('firebase_messaging_sender_id', $reglage->firebase_messaging_sender_id) }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">App ID</label>
                                    <input type="text" class="form-control" name="firebase_app_id" value="{{ old('firebase_app_id', $reglage->firebase_app_id) }}">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
