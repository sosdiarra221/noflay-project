<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $facture->numero }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #212529; margin: 0; padding: 30px; }
        .entete { width: 100%; margin-bottom: 20px; }
        .entete td { vertical-align: top; }
        .societe-nom { font-size: 18px; font-weight: bold; color: #ff7a1a; }
        .societe-details { color: #6c757d; font-size: 11px; line-height: 1.6; }
        .titre-facture { text-align: right; }
        .titre-facture h1 { font-size: 22px; margin: 0; color: #212529; text-transform: uppercase; }
        .titre-facture .numero { font-size: 13px; color: #6c757d; margin-top: 4px; }
        .badge-statut { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; background: #e7f4ee; color: #0ab39c; margin-top: 6px; }
        .source { font-size: 10px; color: #6c757d; font-style: italic; margin-bottom: 20px; }
        .infos-client { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .infos-client td { padding: 12px; background: #f8f9fa; }
        .infos-label { font-size: 10px; text-transform: uppercase; color: #6c757d; margin-bottom: 3px; }
        .infos-valeur { font-size: 13px; font-weight: bold; }
        table.lignes { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.lignes th { background: #ff7a1a; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        table.lignes td { padding: 8px 10px; border-bottom: 1px solid #e9ecef; font-size: 12px; }
        table.lignes th.text-end, table.lignes td.text-end { text-align: right; }
        table.totaux { width: 280px; margin-left: auto; border-collapse: collapse; }
        table.totaux td { padding: 6px 10px; font-size: 12px; }
        table.totaux .total-final td { font-size: 15px; font-weight: bold; border-top: 2px solid #212529; padding-top: 10px; }
        .notes { margin-top: 25px; padding: 12px; background: #f8f9fa; font-size: 11px; color: #495057; }
        .pied { margin-top: 40px; text-align: center; font-size: 10px; color: #adb5bd; }
    </style>
</head>
<body>
    <table class="entete">
        <tr>
            <td style="width: 60%;">
                <div class="societe-nom">{{ $reglage->nom_societe ?: config('app.name') }}</div>
                <div class="societe-details">
                    {{ $reglage->adresse ?: '' }}<br>
                    @if ($reglage->telephone) Tél : {{ $reglage->telephone }}<br> @endif
                    @if ($reglage->email) {{ $reglage->email }} @endif
                </div>
            </td>
            <td class="titre-facture" style="width: 40%;">
                <h1>Facture</h1>
                <div class="numero">N&deg; {{ $facture->numero }}</div>
                <div class="numero">Date : {{ $facture->date_facture->format('d/m/Y') }}</div>
                <div class="badge-statut">{{ $facture->libelleStatut() }}</div>
            </td>
        </tr>
    </table>

    @if ($facture->source)
        <div class="source">Source : {{ $facture->source }}</div>
    @endif

    <table class="infos-client">
        <tr>
            <td>
                <div class="infos-label">Client / Prospect</div>
                <div class="infos-valeur">{{ $facture->client->nom_complet }}</div>
                @if ($facture->client->telephone)<div>{{ $facture->client->telephone }}</div>@endif
                @if ($facture->client->email)<div>{{ $facture->client->email }}</div>@endif
            </td>
        </tr>
    </table>

    <table class="lignes">
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-end">Quantité</th>
                <th class="text-end">Prix unitaire</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facture->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->designation }}</td>
                    <td class="text-end">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">{{ number_format($ligne->total, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totaux">
        <tr>
            <td>Sous-total HT</td>
            <td class="text-end">{{ number_format($facture->sous_total_ht, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if ($facture->appliquer_tva)
            <tr>
                <td>TVA ({{ (float) $facture->taux_tva }} %)</td>
                <td class="text-end">{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCFA</td>
            </tr>
        @endif
        <tr class="total-final">
            <td>Total TTC</td>
            <td class="text-end">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    @if ($facture->notes)
        <div class="notes">{{ $facture->notes }}</div>
    @endif

    <div class="pied">Facture générée le {{ now()->format('d/m/Y à H:i') }} — {{ $reglage->nom_societe ?: config('app.name') }}</div>
</body>
</html>
