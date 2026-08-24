@csrf
@if ($utilisateur ?? null)
    @method('PUT')
@endif

<div class="card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nom complet<span class="text-danger ms-1">*</span></label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $utilisateur->name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $utilisateur->email ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Identifiant (optionnel)</label>
            <input type="text" class="form-control" name="identifiant" value="{{ old('identifiant', $utilisateur->identifiant ?? '') }}" placeholder="Ex: mfall">
            <div class="fs-11 text-muted mt-1">Permet de se connecter sans utiliser l'email.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Rôle<span class="text-danger ms-1">*</span></label>
            <select class="form-select" name="role_id" required>
                <option value="">Sélectionner...</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id', $utilisateur->role_id ?? '') == $role->id)>{{ $role->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Département</label>
            <select class="form-select" name="departement_id">
                <option value="">—</option>
                @foreach ($departements as $departement)
                    <option value="{{ $departement->id }}" @selected(old('departement_id', $utilisateur->departement_id ?? '') == $departement->id)>{{ $departement->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Statut<span class="text-danger ms-1">*</span></label>
            <select class="form-select" name="statut" required>
                <option value="actif" @selected(old('statut', $utilisateur->statut ?? 'actif') === 'actif')>Actif</option>
                <option value="inactif" @selected(old('statut', $utilisateur->statut ?? '') === 'inactif')>Inactif</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mot de passe{{ isset($utilisateur) ? '' : '*' }}</label>
            <input type="password" class="form-control" name="password" placeholder="{{ isset($utilisateur) ? 'Laisser vide pour ne pas changer' : 'Minimum 8 caractères' }}" {{ isset($utilisateur) ? '' : 'required' }}>
        </div>
        <div class="col-md-6">
            <label class="form-label">Code PIN (4 chiffres)</label>
            <input type="text" inputmode="numeric" maxlength="4" class="form-control" name="code_pin" placeholder="{{ isset($utilisateur) ? 'Laisser vide pour ne pas changer' : 'Ex: 1234' }}">
        </div>
    </div>
</div>
<div class="card-footer d-flex justify-content-end gap-2">
    <a href="{{ route('administration.utilisateurs.index') }}" class="btn btn-light">Annuler</a>
    <button type="submit" class="btn btn-primary">{{ isset($utilisateur) ? 'Enregistrer' : "Créer l'utilisateur" }}</button>
</div>
