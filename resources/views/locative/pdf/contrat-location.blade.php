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
        .infos-table td.label { color: #777; width: 200px; }
    </style>
</head>
<body>
    <h1>Contrat de location {{ $contrat->numero }}</h1>
    <p class="muted">Généré le {{ now()->format('d/m/Y à H:i') }}</p>

    <h2>Parties</h2>
    <table class="infos-table">
        <tr><td class="label">Bailleur</td><td>{{ $contrat->bailleur->nom_complet }}</td></tr>
        <tr><td class="label">Locataire</td><td>{{ $contrat->location->locataire->nom_complet }}</td></tr>
        <tr><td class="label">Téléphone locataire</td><td>{{ $contrat->location->locataire->telephone ?: '—' }}</td></tr>
    </table>

    <h2>Bien loué</h2>
    <table class="infos-table">
        <tr><td class="label">Désignation</td><td>{{ $contrat->bien->titre }}</td></tr>
        <tr><td class="label">Adresse</td><td>{{ $contrat->bien->adresse ?: '—' }}</td></tr>
    </table>

    <h2>Conditions</h2>
    <table class="infos-table">
        <tr><td class="label">Numéro de contrat</td><td>{{ $contrat->numero }}</td></tr>
        <tr><td class="label">Période</td><td>{{ $contrat->date_debut->format('d/m/Y') }} @if ($contrat->date_fin) → {{ $contrat->date_fin->format('d/m/Y') }} @endif</td></tr>
        <tr><td class="label">Loyer mensuel</td><td>{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA</td></tr>
        <tr><td class="label">Charges</td><td>{{ number_format($contrat->charges, 0, ',', ' ') }} FCFA</td></tr>
        <tr><td class="label">Dépôt de garantie</td><td>{{ number_format($contrat->depot_garantie, 0, ',', ' ') }} FCFA</td></tr>
        <tr><td class="label">Jour d'échéance</td><td>{{ $contrat->jour_echeance }} de chaque mois</td></tr>
    </table>

    @if ($contrat->notes)
        <h2>Notes</h2>
        <p>{{ $contrat->notes }}</p>
    @endif
</body>
</html>
