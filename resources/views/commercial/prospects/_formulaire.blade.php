<div class="modal-header">
    <h5 class="modal-title">Nouvelle prospection</h5>
    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
        <i class="ri-close-large-line fw-semibold"></i>
    </button>
</div>
<div class="modal-body">
    @if (session('doublon'))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <strong>Prospect similaire trouvé :</strong> {{ session('doublon')->nom_complet }} — {{ session('doublon')->telephone }}
            <div class="mt-2 d-flex gap-2">
                <a href="{{ route('commercial.prospects.show', session('doublon')) }}" class="btn btn-sm btn-light-primary">Ouvrir sa fiche</a>
            </div>
        </div>
    @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="{{ old('prenom') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
            <input type="text" class="form-control" name="nom" value="{{ old('nom') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Téléphone<span class="text-danger ms-1">*</span></label>
            <input type="text" class="form-control" name="telephone" value="{{ old('telephone') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
        </div>
        <div class="col-md-12">
            <label class="form-label">Adresse</label>
            <input type="text" class="form-control" name="adresse" value="{{ old('adresse') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Type de demande</label>
            <select class="form-select" name="type_demande_id">
                <option value="">—</option>
                @foreach ($typesDemande as $type)
                    <option value="{{ $type->id }}" @selected(old('type_demande_id') == $type->id)>{{ $type->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Source<span class="text-danger ms-1">*</span></label>
            <select class="form-select" name="source_id" required>
                <option value="">—</option>
                @foreach ($sources as $source)
                    <option value="{{ $source->id }}" @selected(old('source_id') == $source->id)>{{ $source->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Devise</label>
            <select class="form-select" name="devise">
                <option value="FCFA" selected>FCFA</option>
                <option value="EURO">EURO</option>
                <option value="DOLLAR">DOLLAR</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Budget minimum</label>
            <input type="number" step="0.01" min="0" class="form-control" name="budget_min" value="{{ old('budget_min') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Budget maximum</label>
            <input type="number" step="0.01" min="0" class="form-control" name="budget_max" value="{{ old('budget_max') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Décrivez le besoin</label>
            <textarea class="form-control" name="besoin" rows="3">{{ old('besoin') }}</textarea>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
    @if (session('doublon'))
        <button type="submit" name="forcer_creation" value="1" class="btn btn-warning">Créer quand même</button>
    @else
        <button type="submit" class="btn btn-primary">Créer la prospection</button>
    @endif
</div>
