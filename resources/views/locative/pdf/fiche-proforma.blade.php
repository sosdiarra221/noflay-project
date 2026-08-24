<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $fiche->numero_reference }}</title>
    @include('locative.pdf.partials.quittance-style')
</head>
<body>
    <div class="conteneur">
        @include('locative.pdf.partials.quittance-entete')

        <table class="bandeau-table">
            <tr>
                <td class="bandeau-titre">
                    FICHE PROFORMA DE LOYER
                    <div class="bandeau-soustitre">À l'attention du locataire</div>
                </td>
                <td class="bandeau-numero">
                    <div>N&deg; Réf : {{ $fiche->numero_reference }}</div>
                    <div>Date : {{ $fiche->created_at->versionLongue() }}</div>
                </td>
            </tr>
        </table>

        <table class="boites-table">
            <tr>
                <td class="boite">
                    <div class="boite-entete">Informations sur le bien</div>
                    <div class="boite-corps">
                        <strong>Locataire :</strong> {{ $contrat->location->locataire->nom_complet }}<br>
                        <strong>Bien :</strong> {{ $contrat->bien->titre }}<br>
                        <strong>Adresse :</strong> {{ $contrat->bien->adresse ?: $ville }}@if ($contrat->bien->zone), {{ $contrat->bien->zone }}@endif
                    </div>
                </td>
                <td class="boite">
                    <div class="boite-entete">Période concernée</div>
                    <div class="boite-corps">
                        <strong>Mois :</strong> {{ ucfirst(\Carbon\Carbon::createFromDate($fiche->annee, $fiche->mois, 1)->translatedFormat('F Y')) }}<br>
                        <strong>Période :</strong> du {{ \Carbon\Carbon::createFromDate($fiche->annee, $fiche->mois, 1)->format('d/m/Y') }}
                        au {{ \Carbon\Carbon::createFromDate($fiche->annee, $fiche->mois, 1)->endOfMonth()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="designation-table">
            <tr>
                <th style="text-align: left;">Désignation</th>
                <th class="col-montant">Montant (FCFA)</th>
                <th class="col-obs">Observations</th>
            </tr>
            <tr>
                <td>Loyer mensuel</td>
                <td class="col-montant">{{ number_format($fiche->loyer_mensuel, 0, ',', ' ') }}</td>
                <td>Loyer du mois de {{ \Carbon\Carbon::createFromDate($fiche->annee, $fiche->mois, 1)->translatedFormat('F Y') }}</td>
            </tr>
            <tr>
                <td>Arriérés de loyer</td>
                <td class="col-montant">{{ number_format($fiche->arrieres, 0, ',', ' ') }}</td>
                <td>{{ $fiche->arrieres > 0 ? 'Solde impayé des mois précédents' : 'Aucun arriéré à ce jour' }}</td>
            </tr>
            <tr>
                <td>Frais d'agence</td>
                <td class="col-montant">{{ number_format($fiche->frais_agence, 0, ',', ' ') }}</td>
                <td>{{ $fiche->frais_agence > 0 ? 'Frais de gestion applicables' : 'Non applicable pour ce mois' }}</td>
            </tr>
            <tr>
                <td>Taxe d'habitation (TOM) &ndash; {{ rtrim(rtrim(number_format($fiche->taux_tom, 2, ',', ' '), '0'), ',') }}%</td>
                <td class="col-montant">{{ number_format($fiche->montant_tom ?: $fiche->taxe_tom, 0, ',', ' ') }}</td>
                <td>Sur le loyer mensuel ({{ number_format($fiche->loyer_mensuel, 0, ',', ' ') }} FCFA)</td>
            </tr>
            <tr>
                <td>Taxe sur la Valeur Ajoutée (TVA) &ndash; {{ rtrim(rtrim(number_format($fiche->taux_tva, 2, ',', ' '), '0'), ',') }}%</td>
                <td class="col-montant">{{ number_format($fiche->montant_tva, 0, ',', ' ') }}</td>
                <td>Sur le loyer mensuel ({{ number_format($fiche->loyer_mensuel, 0, ',', ' ') }} FCFA)</td>
            </tr>
            <tr class="ligne-total">
                <td>Montant total à payer</td>
                <td class="col-montant" colspan="2">{{ number_format($fiche->montant_total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>

        <div class="note-box">
            <strong>Mode de paiement :</strong> Le paiement du loyer est à effectuer avant le
            <strong>{{ optional($fiche->date_limite_paiement)->versionLongue() }}</strong>, sur le compte ou par le moyen convenu.
            Merci de bien vouloir le respecter pour éviter tout désagrément.
        </div>

        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div>Fait à {{ $ville }}, le {{ $fiche->created_at->versionLongue() }}</div>
                    <div class="signature-pour">Pour {{ $reglage->nom_societe ?: config('app.name') }}</div>
                    <div class="signature-titre">La Direction</div>
                </td>
            </tr>
        </table>

        @include('locative.pdf.partials.quittance-pied')
    </div>
</body>
</html>
