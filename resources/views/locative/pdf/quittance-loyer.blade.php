<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $numero }}</title>
    @include('locative.pdf.partials.quittance-style')
</head>
<body>
    <div class="conteneur">
        @include('locative.pdf.partials.quittance-entete')

        <table class="bandeau-table">
            <tr>
                <td class="bandeau-titre">REÇU DE QUITTANCE DE LOYER</td>
                <td class="bandeau-numero">
                    <div>N&deg; {{ $numero }}</div>
                    <div>Date : {{ now()->versionLongue() }}</div>
                </td>
            </tr>
        </table>

        <div class="infos-box">
            <table class="infos-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="infos-label">Locataire :</div>
                        <div class="infos-valeur">{{ $echeance->contratLocation->location->locataire->nom_complet }}</div>
                    </td>
                    <td>
                        <div class="infos-label">Bien :</div>
                        <div class="infos-valeur">{{ $echeance->contratLocation->bien->titre }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="montants-table">
            <tr>
                <th>Période</th>
                <th>Montant du loyer</th>
                <th>Montant payé</th>
            </tr>
            <tr>
                <td>{{ ucfirst(\Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y')) }}</td>
                <td>{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>

        <p class="confirmation">
            Nous confirmons avoir reçu la somme de <strong>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</strong>
            au titre du loyer pour la période susmentionnée.
        </p>

        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div>Fait à {{ $ville }}, le {{ now()->versionLongue() }}</div>
                    <div class="signature-pour">Pour {{ $reglage->nom_societe ?: config('app.name') }}</div>
                    <div class="signature-titre">Le Directeur</div>
                </td>
            </tr>
        </table>

        @include('locative.pdf.partials.quittance-pied')
    </div>
</body>
</html>
