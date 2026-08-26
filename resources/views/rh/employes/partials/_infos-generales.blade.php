@if ($alerteCdd)
    <div class="card border-warning">
        <div class="card-header bg-warning-subtle">
            <h6 class="card-action-title mb-0 text-warning-emphasis"><i class="bi bi-exclamation-triangle me-1"></i>Alerte CDD</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $employe->prenom }} {{ $employe->nom }} a déjà eu <strong>{{ $nombreContratsCdd }} contrats CDD</strong>. Le renouvellement d'un CDD est généralement limité par le code du travail — un passage en CDI est à envisager.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-header"><h6 class="card-action-title mb-0">Présentation</h6></div>
        <div class="card-body">
            <p class="mb-0">{{ $presentation }}</p>
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="card-action-title mb-0">Contact</h6></div>
            <div class="card-body">
                <div class="row mb-3"><div class="col-5 text-muted">Téléphone</div><div class="col-7 fw-medium">{{ $employe->telephone ?: '—' }}</div></div>
                <div class="row mb-3"><div class="col-5 text-muted">WhatsApp</div><div class="col-7 fw-medium">{{ $employe->whatsapp ?: '—' }}</div></div>
                <div class="row mb-3"><div class="col-5 text-muted">Email</div><div class="col-7 fw-medium">{{ $employe->email ?: '—' }}</div></div>
                <div class="row"><div class="col-5 text-muted">Adresse</div><div class="col-7 fw-medium">{{ $employe->adresse ?: '—' }}</div></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h6 class="card-action-title mb-0">Diplômes &amp; langues</h6></div>
            <div class="card-body">
                @forelse ($employe->diplomes as $diplome)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $diplome->intitule }} @if($diplome->niveau) <span class="text-muted fs-12">({{ $diplome->niveau }})</span> @endif</span>
                        <span class="text-muted">{{ $diplome->annee_obtention ?: '—' }}</span>
                    </div>
                @empty
                    <p class="text-muted fs-12 mb-0">Aucun diplôme enregistré.</p>
                @endforelse
                <div class="row mt-3"><div class="col-5 text-muted">Langues parlées</div><div class="col-7 fw-medium">{{ $employe->langues_parlees ?: '—' }}</div></div>
                <div class="row"><div class="col-5 text-muted">Langues lues</div><div class="col-7 fw-medium">{{ $employe->langues_lues ?: '—' }}</div></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        @can('rh.donnees-sensibles')
            <div class="card">
                <div class="card-header"><h6 class="card-action-title mb-0"><i class="bi bi-shield-lock me-1"></i>État civil &amp; identité <span class="badge bg-warning-subtle text-warning ms-1 fs-11">Données sensibles</span></h6></div>
                <div class="card-body">
                    <div class="row mb-3"><div class="col-5 text-muted">Sexe</div><div class="col-7 fw-medium text-capitalize">{{ $employe->sexe ?: '—' }}</div></div>
                    <div class="row mb-3"><div class="col-5 text-muted">Date de naissance</div><div class="col-7 fw-medium">{{ $employe->date_naissance?->format('d/m/Y') ?: '—' }}</div></div>
                    <div class="row mb-3"><div class="col-5 text-muted">Lieu de naissance</div><div class="col-7 fw-medium">{{ $employe->lieu_naissance ?: '—' }}</div></div>
                    <div class="row mb-3"><div class="col-5 text-muted">Situation matrimoniale</div><div class="col-7 fw-medium">{{ \App\Models\Rh\Employe::SITUATIONS_MATRIMONIALES[$employe->situation_matrimoniale] ?? '—' }}</div></div>
                    <div class="row mb-3"><div class="col-5 text-muted">Pièce d'identité</div><div class="col-7 fw-medium">{{ $employe->libellePieceIdentite() ?: '—' }} {{ $employe->piece_identite_numero }}</div></div>
                    <div class="row"><div class="col-5 text-muted">Aptitudes</div><div class="col-7">
                        <span class="badge {{ $employe->permis_conduire ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} me-1">Permis {{ $employe->permis_conduire ? '✓' : '✗' }}</span>
                        <span class="badge {{ $employe->arts_martiaux ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} me-1">Arts martiaux {{ $employe->arts_martiaux ? '✓' : '✗' }}</span>
                        <span class="badge {{ $employe->service_militaire ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">Service militaire {{ $employe->service_militaire ? '✓' : '✗' }}</span>
                    </div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h6 class="card-action-title mb-0"><i class="bi bi-shield-lock me-1"></i>Coordonnées bancaires <span class="badge bg-warning-subtle text-warning ms-1 fs-11">Données sensibles</span></h6></div>
                <div class="card-body">
                    <div class="row mb-3"><div class="col-5 text-muted">Banque</div><div class="col-7 fw-medium">{{ $employe->banque ?: '—' }}</div></div>
                    <div class="row"><div class="col-5 text-muted">Compte / RIB</div><div class="col-7 fw-medium">{{ $employe->compte_bancaire ?: '—' }}</div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h6 class="card-action-title mb-0"><i class="bi bi-shield-lock me-1"></i>Situation familiale <span class="badge bg-warning-subtle text-warning ms-1 fs-11">Données sensibles</span></h6></div>
                <div class="card-body">
                    <p class="text-muted fs-12 mb-2">Épouse(s)</p>
                    @forelse ($employe->epouses as $epouse)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $epouse->nom_complet }}</span>
                            <span class="text-muted">{{ $epouse->telephone ?: '—' }}</span>
                        </div>
                    @empty
                        <p class="text-muted fs-12">Aucune épouse enregistrée.</p>
                    @endforelse
                    <p class="text-muted fs-12 mb-2 mt-3">Enfant(s)</p>
                    @forelse ($employe->enfants as $enfant)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $enfant->nom_complet }}</span>
                            <span class="text-muted">{{ $enfant->date_naissance?->format('d/m/Y') ?: '—' }}</span>
                        </div>
                    @empty
                        <p class="text-muted fs-12 mb-0">Aucun enfant enregistré.</p>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h6 class="card-action-title mb-0">Contact d'urgence</h6></div>
                <div class="card-body">
                    <div class="row mb-3"><div class="col-5 text-muted">Nom</div><div class="col-7 fw-medium">{{ $employe->personne_urgence_nom ?: '—' }}</div></div>
                    <div class="row mb-3"><div class="col-5 text-muted">Téléphone</div><div class="col-7 fw-medium">{{ $employe->personne_urgence_telephone ?: '—' }}</div></div>
                    <div class="row"><div class="col-5 text-muted">Lien de parenté</div><div class="col-7 fw-medium">{{ $employe->personne_urgence_lien ?: '—' }}</div></div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-8">
                    <i class="bi bi-shield-lock text-muted" style="font-size: 2.5rem;"></i>
                    <p class="text-muted mt-3 mb-0">L'identité complète, le RIB et la situation familiale sont des données sensibles réservées à un accès restreint.</p>
                </div>
            </div>
        @endcan
    </div>
</div>
@if ($employe->notes)
    <div class="card">
        <div class="card-header"><h6 class="card-action-title mb-0">Notes</h6></div>
        <div class="card-body"><p class="mb-0">{{ $employe->notes }}</p></div>
    </div>
@endif
