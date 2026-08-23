<div class="modal-header">
    <h5 class="modal-title">{{ $partenaire ? 'Modifier le partenaire' : 'Nouveau partenaire' }}</h5>
    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
        <i class="ri-close-large-line fw-semibold"></i>
    </button>
</div>
<div class="modal-body">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
            <select class="form-select" name="type" required>
                @foreach (\App\Models\Commercial\Partenaire::TYPES as $type)
                    <option value="{{ $type }}" @selected(old('type', $partenaire->type ?? 'apporteur_affaires') === $type)>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
            <input type="text" class="form-control" name="nom" value="{{ old('nom', $partenaire->nom ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Personne à contacter</label>
            <input type="text" class="form-control" name="contact_nom" value="{{ old('contact_nom', $partenaire->contact_nom ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Téléphone</label>
            <input type="text" class="form-control" name="telephone" value="{{ old('telephone', $partenaire->telephone ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $partenaire->email ?? '') }}">
        </div>
        <div class="col-md-8">
            <label class="form-label">Adresse</label>
            <input type="text" class="form-control" name="adresse" value="{{ old('adresse', $partenaire->adresse ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Commission (%)</label>
            <input type="number" step="0.01" min="0" max="100" class="form-control" name="commission_pourcentage" value="{{ old('commission_pourcentage', $partenaire->commission_pourcentage ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Statut</label>
            <select class="form-select" name="statut">
                <option value="actif" @selected(old('statut', $partenaire->statut ?? 'actif') === 'actif')>Actif</option>
                <option value="inactif" @selected(old('statut', $partenaire->statut ?? '') === 'inactif')>Inactif</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="3">{{ old('notes', $partenaire->notes ?? '') }}</textarea>
        </div>
    </div>
</div>
