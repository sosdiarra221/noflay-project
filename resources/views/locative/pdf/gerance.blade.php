<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $gerance->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 24px; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .muted { color: #777; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #f4f4f4; }
        .infos-table td { border: none; padding: 3px 0; }
        .infos-table td.label { color: #777; width: 200px; }
    </style>
</head>
<body>
    <h1>Contrat de gérance {{ $gerance->numero }}</h1>
    <p class="muted">Généré le {{ now()->format('d/m/Y à H:i') }}</p>

    <h2>Bailleur</h2>
    <table class="infos-table">
        <tr><td class="label">Nom</td><td>{{ $gerance->bailleur->nom_complet }}</td></tr>
        <tr><td class="label">Téléphone</td><td>{{ $gerance->bailleur->telephone ?: '—' }}</td></tr>
        <tr><td class="label">Email</td><td>{{ $gerance->bailleur->email ?: '—' }}</td></tr>
        <tr><td class="label">Adresse</td><td>{{ $gerance->bailleur->adresse ?: '—' }}</td></tr>
    </table>

    <h2>Contrat</h2>
    <table class="infos-table">
        <tr><td class="label">Numéro</td><td>{{ $gerance->numero }}</td></tr>
        <tr><td class="label">Période</td><td>{{ $gerance->date_debut->format('d/m/Y') }} @if ($gerance->date_fin) → {{ $gerance->date_fin->format('d/m/Y') }} @endif</td></tr>
        <tr><td class="label">Type de gérance</td><td>{{ str_replace('_', ' ', ucfirst($gerance->type_gerance)) }}</td></tr>
        <tr><td class="label">Statut</td><td>{{ str_replace('_', ' ', ucfirst($gerance->statut)) }}</td></tr>
        <tr><td class="label">Frais de gestion</td><td>{{ $gerance->frais_gestion_valeur }}{{ $gerance->frais_gestion_mode === 'pourcentage' ? ' %' : ' FCFA' }}</td></tr>
        <tr><td class="label">TVA à la charge</td><td>{{ ucfirst($gerance->tva_charge) }}</td></tr>
        <tr><td class="label">Taxe à la charge</td><td>{{ ucfirst($gerance->taxe_charge) }}</td></tr>
        <tr><td class="label">TOM à la charge</td><td>{{ ucfirst($gerance->tom_charge) }}</td></tr>
    </table>

    <h2>Biens concernés ({{ $gerance->biens->count() }})</h2>
    <table>
        <thead>
            <tr><th>Bien</th><th>Type</th><th>Montant</th><th>Statut</th></tr>
        </thead>
        <tbody>
            @forelse ($gerance->biens as $bien)
                <tr>
                    <td>{{ $bien->titre }}</td>
                    <td>{{ ucfirst($bien->type_exploitation) }}</td>
                    <td>{{ number_format($bien->montantExploitation() ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td>{{ str_replace('_', ' ', ucfirst($bien->statut)) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun bien rattaché.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($gerance->notes)
        <h2>Notes</h2>
        <p>{{ $gerance->notes }}</p>
    @endif
</body>
</html>
