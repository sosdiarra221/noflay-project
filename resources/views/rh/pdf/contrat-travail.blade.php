<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $contrat->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 24px; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .muted { color: #777; }
        .infos-table { width: 100%; }
        .infos-table td { border: none; padding: 3px 0; }
        .infos-table td.label { color: #777; width: 220px; }
    </style>
</head>
<body>
    <h1>Contrat de travail {{ $contrat->numero }}</h1>
    <p class="muted">{{ $reglage->nom_societe ?: config('app.name') }} — Généré le {{ now()->format('d/m/Y à H:i') }}</p>

    <h2>Parties</h2>
    <table class="infos-table">
        <tr><td class="label">Employeur</td><td>{{ $reglage->nom_societe ?: config('app.name') }}</td></tr>
        <tr><td class="label">Employé</td><td>{{ $contrat->employe->nom_complet }}</td></tr>
        <tr><td class="label">Matricule</td><td>{{ $contrat->employe->matricule }}</td></tr>
    </table>

    <h2>Poste &amp; rattachement</h2>
    <table class="infos-table">
        <tr><td class="label">Poste</td><td>{{ $contrat->employe->poste->nom ?? '—' }}</td></tr>
        <tr><td class="label">Fonction</td><td>{{ $contrat->employe->libelleCategorieFonction() }}</td></tr>
        <tr><td class="label">Département</td><td>{{ $contrat->employe->departement->nom ?? '—' }}</td></tr>
    </table>

    <h2>Conditions du contrat</h2>
    <table class="infos-table">
        <tr><td class="label">Numéro de contrat</td><td>{{ $contrat->numero }}</td></tr>
        <tr><td class="label">Type de contrat</td><td>{{ $contrat->libelleType() }}</td></tr>
        <tr><td class="label">Date de début</td><td>{{ $contrat->date_debut->format('d/m/Y') }}</td></tr>
        <tr><td class="label">Date de fin prévue</td><td>{{ $contrat->date_prevu_fin?->format('d/m/Y') ?: '—' }}</td></tr>
        <tr><td class="label">Date de fin réelle</td><td>{{ $contrat->date_fin?->format('d/m/Y') ?: '—' }}</td></tr>
        <tr><td class="label">Rémunération</td><td>{{ $contrat->montant ? number_format($contrat->montant, 0, ',', ' ').' FCFA' : '—' }}</td></tr>
        <tr><td class="label">État</td><td>{{ $contrat->etat === 'actif' ? 'Actif' : 'Clôturé' }}</td></tr>
    </table>

    @if ($contrat->motif)
        <h2>Motif / observations</h2>
        <p>{{ $contrat->motif }}</p>
    @endif
</body>
</html>
