@php $modeLocation = old('type_exploitation', $bien->type_exploitation ?? 'location') === 'location'; @endphp

<input type="hidden" name="bailleur_id" value="{{ $gerance->bailleur_id }}">
<input type="hidden" name="gerance_id" value="{{ $gerance->id }}">

<div class="modal-header">
    <h5 class="modal-title">{{ $bien ? 'Modifier le bien' : 'Ajouter un bien' }}</h5>
    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
        <i class="ri-close-large-line fw-semibold"></i>
    </button>
</div>
<div class="modal-body">
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Titre du bien<span class="text-danger ms-1">*</span></label>
            <input type="text" class="form-control" name="titre" value="{{ old('titre', $bien->titre ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Catégorie</label>
            <select class="form-select" name="categorie_bien_id">
                <option value="">—</option>
                @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected(old('categorie_bien_id', $bien->categorie_bien_id ?? '') == $categorie->id)>{{ $categorie->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Adresse</label>
            <input type="text" class="form-control" name="adresse" value="{{ old('adresse', $bien->adresse ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Zone / quartier</label>
            <input type="text" class="form-control" name="zone" value="{{ old('zone', $bien->zone ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="2">{{ old('description', $bien->description ?? '') }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Type d'exploitation<span class="text-danger ms-1">*</span></label>
            <select class="form-select" name="type_exploitation" required>
                <option value="location" @selected($modeLocation)>Location</option>
                <option value="vente" @selected(! $modeLocation)>Vente</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Loyer mensuel (FCFA)</label>
            <input type="number" step="0.01" min="0" class="form-control" name="loyer_mensuel" value="{{ old('loyer_mensuel', $bien->loyer_mensuel ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Prix de vente (FCFA)</label>
            <input type="number" step="0.01" min="0" class="form-control" name="prix_vente" value="{{ old('prix_vente', $bien->prix_vente ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Frais de gestion — mode</label>
            <select class="form-select" name="frais_gestion_mode">
                <option value="">Hériter du contrat de gérance</option>
                <option value="pourcentage" @selected(old('frais_gestion_mode', $bien->frais_gestion_mode ?? '') === 'pourcentage')>Pourcentage</option>
                <option value="montant_fixe" @selected(old('frais_gestion_mode', $bien->frais_gestion_mode ?? '') === 'montant_fixe')>Montant fixe</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Frais de gestion — valeur</label>
            <input type="number" step="0.01" min="0" class="form-control" name="frais_gestion_valeur" value="{{ old('frais_gestion_valeur', $bien->frais_gestion_valeur ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Statut<span class="text-danger ms-1">*</span></label>
            <select class="form-select" name="statut" required>
                <optgroup label="Location">
                    @foreach (\App\Models\Bien::STATUTS_LOCATION as $statutOption)
                        <option value="{{ $statutOption }}" @selected(old('statut', $bien->statut ?? 'disponible') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Vente">
                    @foreach (\App\Models\Bien::STATUTS_VENTE as $statutOption)
                        <option value="{{ $statutOption }}" @selected(old('statut', $bien->statut ?? '') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="2">{{ old('notes', $bien->notes ?? '') }}</textarea>
        </div>
    </div>
</div>
