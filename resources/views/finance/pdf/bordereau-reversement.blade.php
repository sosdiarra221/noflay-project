<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $reversement->numero }}</title>
    @include('locative.pdf.partials.quittance-style')
</head>
<body>
    <div class="conteneur">
        @include('locative.pdf.partials.quittance-entete')

        <table class="bandeau-table">
            <tr>
                <td class="bandeau-titre">
                    BORDEREAU DE REVERSEMENT
                    <div class="bandeau-soustitre">Reversement au bailleur</div>
                </td>
                <td class="bandeau-numero">
                    <div>N&deg; {{ $reversement->numero }}</div>
                    <div>Date : {{ optional($reversement->date_versement)->versionLongue() }}</div>
                </td>
            </tr>
        </table>

        <div class="infos-box">
            <table class="infos-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="infos-label">Bailleur :</div>
                        <div class="infos-valeur">{{ $reversement->bailleur->nom_complet }}</div>
                    </td>
                    <td>
                        <div class="infos-label">Période :</div>
                        <div class="infos-valeur">{{ ucfirst(\Carbon\Carbon::createFromDate($reversement->periode_annee, $reversement->periode_mois, 1)->translatedFormat('F Y')) }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 10px;">
                        <div class="infos-label">Mode de paiement :</div>
                        <div class="infos-valeur">{{ $reversement->modePaiement->nom ?? '—' }}</div>
                    </td>
                    <td style="padding-top: 10px;">
                        <div class="infos-label">Référence :</div>
                        <div class="infos-valeur">{{ $reversement->reference ?: '—' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="montants-table">
            <tr>
                <th>Montant encaissé</th>
                <th>Frais de gestion</th>
                <th>Net reversé</th>
            </tr>
            <tr>
                <td>{{ number_format($reversement->montant_encaisse, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($reversement->montant_frais_gestion, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($reversement->montant_net, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>

        <p class="confirmation">
            Nous confirmons avoir reversé à <strong>{{ $reversement->bailleur->nom_complet }}</strong>
            la somme de <strong>{{ number_format($reversement->montant_net, 0, ',', ' ') }} FCFA</strong>,
            correspondant au montant net des loyers encaissés pour son compte au titre de la période susmentionnée,
            après déduction des frais de gestion convenus.
        </p>

        @if ($reversement->notes)
            <p class="confirmation">Notes : {{ $reversement->notes }}</p>
        @endif

        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div>Fait à {{ $ville }}, le {{ now()->versionLongue() }}</div>
                    <div class="signature-pour">Pour {{ $reglage->nom_societe ?: config('app.name') }}</div>
                    <div class="signature-titre">{{ $reversement->effectuePar->name ?? 'La Direction' }}</div>
                </td>
            </tr>
        </table>

        @include('locative.pdf.partials.quittance-pied')
    </div>
</body>
</html>
