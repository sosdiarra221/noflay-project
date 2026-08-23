<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $paiement->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .muted { color: #777; }
        .infos-table { width: 100%; margin-top: 16px; }
        .infos-table td { border: none; padding: 4px 0; }
        .infos-table td.label { color: #777; width: 220px; }
        .montant { font-size: 20px; font-weight: bold; margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Reçu de paiement {{ $paiement->numero }}</h1>
    <p class="muted">Émis le {{ now()->format('d/m/Y à H:i') }}</p>

    <table class="infos-table">
        <tr><td class="label">Locataire</td><td>{{ $paiement->echeance->contratLocation->location->locataire->nom_complet }}</td></tr>
        <tr><td class="label">Bien</td><td>{{ $paiement->echeance->contratLocation->bien->titre }}</td></tr>
        <tr><td class="label">Période</td><td>{{ \Carbon\Carbon::createFromDate($paiement->echeance->annee, $paiement->echeance->mois, 1)->translatedFormat('F Y') }}</td></tr>
        <tr><td class="label">Mode de paiement</td><td>{{ $paiement->modePaiement->nom ?? '—' }}</td></tr>
        <tr><td class="label">Date</td><td>{{ $paiement->date_paiement->format('d/m/Y') }}</td></tr>
        <tr><td class="label">Référence</td><td>{{ $paiement->reference ?: '—' }}</td></tr>
        <tr><td class="label">Solde restant sur l'échéance</td><td>{{ number_format(max($paiement->echeance->montant_attendu - $paiement->echeance->montant_paye, 0), 0, ',', ' ') }} FCFA</td></tr>
        <tr><td class="label">Enregistré par</td><td>{{ $paiement->enregistrePar->name ?? '—' }}</td></tr>
    </table>

    <p class="montant">Montant payé : {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
</body>
</html>
