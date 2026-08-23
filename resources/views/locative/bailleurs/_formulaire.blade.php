@php $id = $bailleur->id ?? 'new'; @endphp

<div class="modal-header">
    <h5 class="modal-title">{{ $bailleur ? 'Modifier le bailleur' : 'Nouveau bailleur' }}</h5>
    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
        <i class="ri-close-large-line fw-semibold"></i>
    </button>
</div>
<div class="modal-body">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
            <select class="form-select" name="type" required>
                <option value="particulier" @selected(old('type', $bailleur->type ?? 'particulier') === 'particulier')>Particulier</option>
                <option value="entreprise" @selected(old('type', $bailleur->type ?? '') === 'entreprise')>Entreprise</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nom{{ ($bailleur->type ?? 'particulier') === 'entreprise' ? ' / Raison sociale' : '' }}<span class="text-danger ms-1">*</span></label>
            <input type="text" class="form-control" name="nom" value="{{ old('nom', $bailleur->nom ?? '') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="{{ old('prenom', $bailleur->prenom ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Téléphone</label>
            <input type="text" class="form-control" name="telephone" value="{{ old('telephone', $bailleur->telephone ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">WhatsApp</label>
            <input type="text" class="form-control" name="whatsapp" value="{{ old('whatsapp', $bailleur->whatsapp ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $bailleur->email ?? '') }}">
        </div>
        <div class="col-md-8">
            <label class="form-label">Adresse</label>
            <input type="text" class="form-control" name="adresse" value="{{ old('adresse', $bailleur->adresse ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Statut</label>
            <select class="form-select" name="statut">
                <option value="actif" @selected(old('statut', $bailleur->statut ?? 'actif') === 'actif')>Actif</option>
                <option value="inactif" @selected(old('statut', $bailleur->statut ?? '') === 'inactif')>Inactif</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Pièce d'identité — type</label>
            <input type="text" class="form-control" name="piece_identite_type" value="{{ old('piece_identite_type', $bailleur->piece_identite_type ?? '') }}" placeholder="CNI, Passeport...">
        </div>
        <div class="col-md-4">
            <label class="form-label">Pièce d'identité — numéro</label>
            <input type="text" class="form-control" name="piece_identite_numero" value="{{ old('piece_identite_numero', $bailleur->piece_identite_numero ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">NINEA</label>
            <input type="text" class="form-control" name="ninea" value="{{ old('ninea', $bailleur->ninea ?? '') }}" placeholder="Si entreprise">
        </div>
        <div class="col-12">
            <label class="form-label">Coordonnées de paiement</label>
            <textarea class="form-control" name="coordonnees_paiement" rows="2" placeholder="RIB, numéro Wave/Orange Money...">{{ old('coordonnees_paiement', $bailleur->coordonnees_paiement ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="2">{{ old('notes', $bailleur->notes ?? '') }}</textarea>
        </div>
    </div>
</div>
