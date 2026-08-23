<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $paiement->numero }}</title>
    @include('locative.pdf.partials.quittance-style')
</head>
<body>
    <div class="conteneur">
        @include('locative.pdf.partials.quittance-entete')

        <table class="bandeau-table">
            <tr>
                <td class="bandeau-titre">REÇU DE PAIEMENT</td>
                <td class="bandeau-numero">
                    <div>N&deg; {{ $paiement->numero }}</div>
                    <div>Date : {{ $paiement->date_paiement->versionLongue() }}</div>
                </td>
            </tr>
        </table>

        <div class="infos-box">
            <table class="infos-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="infos-label">Locataire :</div>
                        <div class="infos-valeur">{{ $paiement->echeance->contratLocation->location->locataire->nom_complet }}</div>
                    </td>
                    <td>
                        <div class="infos-label">Bien :</div>
                        <div class="infos-valeur">{{ $paiement->echeance->contratLocation->bien->titre }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 10px;">
                        <div class="infos-label">Mode de paiement :</div>
                        <div class="infos-valeur">{{ $paiement->modePaiement->nom ?? '—' }}</div>
                    </td>
                    <td style="padding-top: 10px;">
                        <div class="infos-label">Référence :</div>
                        <div class="infos-valeur">{{ $paiement->reference ?: '—' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="montants-table">
            <tr>
                <th>Période concernée</th>
                <th>Montant du loyer</th>
                <th>Montant de ce paiement</th>
            </tr>
            <tr>
                <td>{{ ucfirst(\Carbon\Carbon::createFromDate($paiement->echeance->annee, $paiement->echeance->mois, 1)->translatedFormat('F Y')) }}</td>
                <td>{{ number_format($paiement->echeance->montant_attendu, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>

        <p class="confirmation">
            Nous confirmons avoir reçu de <strong>{{ $paiement->echeance->contratLocation->location->locataire->nom_complet }}</strong>
            la somme de <strong>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong>
            par {{ strtolower($paiement->modePaiement->nom ?? 'paiement') }}, en règlement partiel ou total du loyer de
            {{ \Carbon\Carbon::createFromDate($paiement->echeance->annee, $paiement->echeance->mois, 1)->translatedFormat('F Y') }}.
            Solde restant sur cette échéance : <strong>{{ number_format(max($paiement->echeance->montant_attendu - $paiement->echeance->montant_paye, 0), 0, ',', ' ') }} FCFA</strong>.
        </p>

        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div>Fait à {{ $ville }}, le {{ now()->versionLongue() }}</div>
                    <div class="signature-pour">Pour {{ $reglage->nom_societe ?: config('app.name') }}</div>
                    <div class="signature-titre">{{ $paiement->enregistrePar->name ?? 'Le Directeur' }}</div>
                </td>
            </tr>
        </table>

        @include('locative.pdf.partials.quittance-pied')
    </div>
</body>
</html>
