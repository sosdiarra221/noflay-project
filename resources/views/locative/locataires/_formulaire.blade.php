<div class="modal-header">
    <h5 class="modal-title">{{ $locataire ? 'Modifier le locataire' : 'Nouveau locataire' }}</h5>
    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
        <i class="ri-close-large-line fw-semibold"></i>
    </button>
</div>
<div class="modal-body">
    <div class="row g-3" data-form-type-toggle>
        <div class="col-md-6 champ-prenom">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="{{ old('prenom', $locataire->prenom ?? '') }}">
        </div>
        <div class="col-md-6 champ-nom" data-col-defaut="col-md-6" data-col-entreprise="col-md-12">
            <label class="form-label"><span class="texte-label-nom">Nom</span><span class="text-danger ms-1">*</span></label>
            <input type="text" class="form-control" name="nom" value="{{ old('nom', $locataire->nom ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Téléphone</label>
            <input type="text" class="form-control" name="telephone" value="{{ old('telephone', $locataire->telephone ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">WhatsApp</label>
            <input type="text" class="form-control" name="whatsapp" value="{{ old('whatsapp', $locataire->whatsapp ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $locataire->email ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Adresse</label>
            <input type="text" class="form-control" name="adresse" value="{{ old('adresse', $locataire->adresse ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Type</label>
            <select class="form-select" name="type_locataire">
                <option value="particulier" @selected(old('type_locataire', $locataire->type_locataire ?? 'particulier') === 'particulier')>Particulier</option>
                <option value="entreprise" @selected(old('type_locataire', $locataire->type_locataire ?? '') === 'entreprise')>Entreprise</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Pièce d'identité — type</label>
            <select class="form-select" name="piece_identite_type">
                <option value="">—</option>
                @foreach (['CNI', 'Passeport', 'Permis de conduire', 'Carte de séjour'] as $piece)
                    <option value="{{ $piece }}" @selected(old('piece_identite_type', $locataire->piece_identite_type ?? '') === $piece)>{{ $piece }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Pièce d'identité — numéro</label>
            <input type="text" class="form-control" name="piece_identite_numero" value="{{ old('piece_identite_numero', $locataire->piece_identite_numero ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Statut</label>
            <select class="form-select" name="statut">
                <option value="actif" @selected(old('statut', $locataire->statut ?? 'actif') === 'actif')>Actif</option>
                <option value="inactif" @selected(old('statut', $locataire->statut ?? '') === 'inactif')>Inactif</option>
            </select>
        </div>
        <div class="col-md-6 champ-entreprise">
            <label class="form-label">NINEA</label>
            <input type="text" class="form-control" name="ninea" value="{{ old('ninea', $locataire->ninea ?? '') }}">
        </div>
        <div class="col-md-6 champ-entreprise">
            <label class="form-label">RC (Registre de Commerce)</label>
            <input type="text" class="form-control" name="rc" value="{{ old('rc', $locataire->rc ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="2">{{ old('notes', $locataire->notes ?? '') }}</textarea>
        </div>
    </div>
</div>
