@extends('partials.layouts.master-administration')

@section('title', 'Réglages | Administration')
@section('title-sub', 'Administration')
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
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="card-body h-176px"
                style="background-image: url('assets/images/background.png');background-repeat: no-repeat;background-position: right;">
            </div>
            <div class="mt-2">
                <div class="card-body p-5">
                    <div class="d-flex flex-wrap align-items-start gap-5">
                        <div class="mt-n12 flex-shrink-0">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $reglage->logo ? asset($reglage->logo) : 'assets/images/logo-md.png' }}"
                                    alt="Logo de la société"
                                    class="h-128px w-128px border border-4 border-white shadow-lg bg-white object-fit-contain">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <h5 class="mb-1">{{ $reglage->nom_societe ?: 'Ma Société' }}</h5>
                                <p class="text-muted fs-12 mb-0">Informations et configuration générale de l'application
                                </p>
                            </div>
                            <div class="row g-5 mt-2">
                                <div class="col-md-4 col-xl-3">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-mailbox fs-16"></i>
                                        <p class="text-muted mb-2">Email</p>
                                    </div>
                                    <h6 class="mb-0">{{ $reglage->email ?: '—' }}</h6>
                                </div>
                                <div class="col-md-4 col-xl-3">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-telephone fs-16"></i>
                                        <p class="text-muted mb-2">Téléphone</p>
                                    </div>
                                    <h6 class="mb-0">{{ $reglage->telephone ?: '—' }}</h6>
                                </div>
                                <div class="col-md-4 col-xl-3">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-currency-exchange fs-16"></i>
                                        <p class="text-muted mb-2">Devise</p>
                                    </div>
                                    <h6 class="mb-0">{{ $reglage->devise?->symbole ?? '—' }}</h6>
                                </div>
                                <div class="col-md-4 col-xl-3">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-envelope-at fs-16"></i>
                                        <p class="text-muted mb-2">Mail sortant</p>
                                    </div>
                                    <h6 class="mb-0 text-uppercase">{{ $reglage->smtp_type }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" id="reglagesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="societe-tab" data-bs-toggle="tab"
                                data-bs-target="#societe-tab-pane" type="button" role="tab"
                                aria-controls="societe-tab-pane" aria-selected="true">Informations société</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="devise-tab" data-bs-toggle="tab"
                                data-bs-target="#devise-tab-pane" type="button" role="tab"
                                aria-controls="devise-tab-pane" aria-selected="false">Devises</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#smtp-tab-pane"
                                type="button" role="tab" aria-controls="smtp-tab-pane"
                                aria-selected="false">Configuration SMTP</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="reglagesTabContent">

                    {{-- Onglet Informations société --}}
                    <div class="tab-pane fade show active" id="societe-tab-pane" role="tabpanel"
                        aria-labelledby="societe-tab" tabindex="0">
                        <div class="card">
                            <div class="card-body p-6">
                                <form action="{{ route('administration.reglages.update-general') }}" method="POST"
                                    enctype="multipart/form-data" class="py-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-5">
                                        <div class="col-lg-4">
                                            <label for="nom_societe" class="form-label">Nom de la société<span
                                                    class="text-danger ms-1">*</span></label>
                                            <input type="text" class="form-control" id="nom_societe"
                                                name="nom_societe" value="{{ old('nom_societe', $reglage->nom_societe) }}"
                                                placeholder="Nom de votre société" required>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ old('email', $reglage->email) }}"
                                                placeholder="contact@societe.com">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="telephone" class="form-label">Téléphone</label>
                                            <input type="text" class="form-control" id="telephone" name="telephone"
                                                value="{{ old('telephone', $reglage->telephone) }}"
                                                placeholder="+221 77 000 00 00">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="ninea" class="form-label">NINEA</label>
                                            <input type="text" class="form-control" id="ninea" name="ninea"
                                                value="{{ old('ninea', $reglage->ninea) }}"
                                                placeholder="NINEA de l'agence">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="site_web" class="form-label">Site web</label>
                                            <input type="text" class="form-control" id="site_web" name="site_web"
                                                value="{{ old('site_web', $reglage->site_web) }}"
                                                placeholder="www.societe.com">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="devise_id" class="form-label">Devise de l'application</label>
                                            <select class="form-select" id="devise_id" name="devise_id">
                                                <option value="">Sélectionner une devise</option>
                                                @foreach ($devises as $devise)
                                                    <option value="{{ $devise->id }}"
                                                        @selected(old('devise_id', $reglage->devise_id) == $devise->id)>
                                                        {{ $devise->nom }} ({{ $devise->symbole }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="logo" class="form-label">Logo de la société</label>
                                            <input type="file" class="form-control" id="logo" name="logo"
                                                accept="image/*">
                                            <small class="text-muted">Format image, 2 Mo maximum.</small>
                                        </div>
                                        <div class="col-lg-12">
                                            <label for="adresse" class="form-label">Adresse</label>
                                            <input type="text" class="form-control" id="adresse" name="adresse"
                                                value="{{ old('adresse', $reglage->adresse) }}"
                                                placeholder="Adresse complète de la société">
                                        </div>
                                        <div class="col-lg-12 d-flex justify-content-end gap-2 flex-shrink-0 mt-8">
                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Onglet Devises --}}
                    <div class="tab-pane fade" id="devise-tab-pane" role="tabpanel" aria-labelledby="devise-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-semibold">
                                        Devises <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $devises->count() }}</span>
                                    </h6>
                                    <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                        data-bs-target="#createDeviseModal">
                                        <i class="bi bi-plus-lg me-1"></i>Ajouter une Devise
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Code</th>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Symbole</th>
                                                <th scope="col">Par défaut</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($devises as $devise)
                                                <tr>
                                                    <td><span class="badge bg-light-subtle text-body">{{ $devise->code }}</span></td>
                                                    <td>{{ $devise->nom }}</td>
                                                    <td>{{ $devise->symbole }}</td>
                                                    <td>
                                                        @if ($devise->est_defaut)
                                                            <span class="badge bg-success-subtle text-success">Oui</span>
                                                        @else
                                                            <span class="badge bg-light-subtle text-body">Non</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <button type="button" class="btn btn-light-primary icon-btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editDeviseModal{{ $devise->id }}"
                                                                data-bs-custom-class="tooltip-white" data-bs-title="Modifier">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <form action="{{ route('devises.destroy', $devise) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Supprimer cette devise ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-light-danger icon-btn-sm"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-custom-class="tooltip-white"
                                                                    data-bs-placement="top" data-bs-title="Supprimer">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-5">Aucune devise
                                                        enregistrée.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @foreach ($devises as $devise)
                            <!-- Edit Devise Modal -->
                            <div class="modal fade" id="editDeviseModal{{ $devise->id }}"
                                tabindex="-1" aria-labelledby="editDeviseModalLabel{{ $devise->id }}"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('devises.update', $devise) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="editDeviseModalLabel{{ $devise->id }}">
                                                    Modifier la Devise</h5>
                                                <button type="button" class="btn-close icon-btn-sm"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="ri-close-large-line fw-semibold"></i>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label">Code</label>
                                                        <input type="text" class="form-control"
                                                            name="code" value="{{ $devise->code }}"
                                                            required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Nom</label>
                                                        <input type="text" class="form-control"
                                                            name="nom" value="{{ $devise->nom }}"
                                                            required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Symbole</label>
                                                        <input type="text" class="form-control"
                                                            name="symbole"
                                                            value="{{ $devise->symbole }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                type="checkbox" value="1"
                                                                name="est_defaut" id="estDefaut{{ $devise->id }}"
                                                                @checked($devise->est_defaut)>
                                                            <label class="form-check-label"
                                                                for="estDefaut{{ $devise->id }}">
                                                                Définir comme devise par défaut
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Fermer</button>
                                                <button type="submit"
                                                    class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Onglet SMTP --}}
                    <div class="tab-pane fade" id="smtp-tab-pane" role="tabpanel" aria-labelledby="smtp-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body p-6">
                                <form action="{{ route('administration.reglages.update-smtp') }}" method="POST" class="py-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-5">
                                        <div class="col-lg-4">
                                            <label for="smtp_type" class="form-label">Type d'envoi<span
                                                    class="text-danger ms-1">*</span></label>
                                            <select class="form-select" id="smtp_type" name="smtp_type" required>
                                                @foreach (['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'resend' => 'Resend', 'log' => 'Journal (log, pour tests)'] as $valeur => $libelle)
                                                    <option value="{{ $valeur }}"
                                                        @selected(old('smtp_type', $reglage->smtp_type) == $valeur)>
                                                        {{ $libelle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="smtp_from_name" class="form-label">Nom de l'expéditeur</label>
                                            <input type="text" class="form-control" id="smtp_from_name"
                                                name="smtp_from_name"
                                                value="{{ old('smtp_from_name', $reglage->smtp_from_name) }}"
                                                placeholder="Nom affiché comme expéditeur">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="smtp_from_address" class="form-label">Email expéditeur</label>
                                            <input type="email" class="form-control" id="smtp_from_address"
                                                name="smtp_from_address"
                                                value="{{ old('smtp_from_address', $reglage->smtp_from_address) }}"
                                                placeholder="no-reply@societe.com">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="smtp_host" class="form-label">Hôte SMTP</label>
                                            <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                                                value="{{ old('smtp_host', $reglage->smtp_host) }}"
                                                placeholder="smtp.gmail.com">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="smtp_port" class="form-label">Port</label>
                                            <input type="text" class="form-control" id="smtp_port" name="smtp_port"
                                                value="{{ old('smtp_port', $reglage->smtp_port) }}" placeholder="587">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="smtp_encryption" class="form-label">Sécurité</label>
                                            <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                                <option value="">Aucune</option>
                                                <option value="tls"
                                                    @selected(old('smtp_encryption', $reglage->smtp_encryption) == 'tls')>TLS</option>
                                                <option value="ssl"
                                                    @selected(old('smtp_encryption', $reglage->smtp_encryption) == 'ssl')>SSL</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="smtp_username" class="form-label">Utilisateur</label>
                                            <input type="text" class="form-control" id="smtp_username"
                                                name="smtp_username"
                                                value="{{ old('smtp_username', $reglage->smtp_username) }}"
                                                placeholder="Identifiant SMTP">
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="smtp_password" class="form-label">Mot de passe</label>
                                            <input type="password" class="form-control" id="smtp_password"
                                                name="smtp_password" placeholder="••••••••" autocomplete="new-password">
                                            <small class="text-muted">Laisser vide pour conserver le mot de passe
                                                actuel.</small>
                                        </div>
                                        <div class="col-lg-12 d-flex justify-content-end gap-2 flex-shrink-0 mt-8">
                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Create Devise Modal -->
        <div class="modal fade" id="createDeviseModal" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="createDeviseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('devises.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createDeviseModalLabel">Ajouter une Devise</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="deviseCode" class="form-label">Code</label>
                                    <input type="text" class="form-control" id="deviseCode" name="code"
                                        placeholder="Ex: XOF" required>
                                </div>
                                <div class="col-12">
                                    <label for="deviseNom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="deviseNom" name="nom"
                                        placeholder="Ex: Franc CFA" required>
                                </div>
                                <div class="col-12">
                                    <label for="deviseSymbole" class="form-label">Symbole</label>
                                    <input type="text" class="form-control" id="deviseSymbole" name="symbole"
                                        placeholder="Ex: FCFA" required>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="est_defaut"
                                            id="deviseEstDefaut">
                                        <label class="form-check-label" for="deviseEstDefaut">
                                            Définir comme devise par défaut
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection

@section('js')
    <!-- App js -->
    <script type="module" src="assets/js/app.js"></script>
@endsection
