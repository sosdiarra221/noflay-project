<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport comptabilité</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        .muted { color: #777; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border-bottom: 1px solid #ddd; padding: 5px 6px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 10px; text-transform: uppercase; }
        td.montant { text-align: right; }
        .stats-table { width: 100%; margin-top: 10px; }
        .stats-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .stats-table .label { font-size: 9px; color: #777; text-transform: uppercase; }
        .stats-table .valeur { font-size: 13px; font-weight: bold; }
        .type-encaissement { color: #1a7a3c; }
        .type-decaissement { color: #b02a2a; }
    </style>
</head>
<body>
    <h1>Rapport de comptabilité générale</h1>
    <p class="muted">Période du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }} — généré le {{ now()->format('d/m/Y à H:i') }}</p>

    <table class="stats-table">
        <tr>
            <td>
                <div class="label">Total encaissé</div>
                <div class="valeur">{{ number_format($stats['total_encaisse'], 0, ',', ' ') }} FCFA</div>
            </td>
            <td>
                <div class="label">Total décaissé</div>
                <div class="valeur">{{ number_format($stats['total_decaisse'], 0, ',', ' ') }} FCFA</div>
            </td>
            <td>
                <div class="label">Solde de la période</div>
                <div class="valeur">{{ number_format($stats['solde_periode'], 0, ',', ' ') }} FCFA</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Source</th>
                <th>Module</th>
                <th>Référence</th>
                <th class="montant">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mouvements as $mouvement)
                <tr>
                    <td>{{ $mouvement->date->format('d/m/Y') }}</td>
                    <td class="type-{{ $mouvement->type }}">{{ $mouvement->type === 'encaissement' ? 'Encaissement' : 'Décaissement' }}</td>
                    <td>{{ $mouvement->source }}</td>
                    <td>{{ $mouvement->module }}</td>
                    <td>{{ $mouvement->reference }}</td>
                    <td class="montant">{{ number_format($mouvement->montant, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">Aucun mouvement sur la période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
