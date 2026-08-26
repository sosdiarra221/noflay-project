<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mandat de gérance — {{ $gerance->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
        .conteneur { padding: 6px 4px; }

        .entete-titre { text-align: center; border-bottom: 2px solid #1a3c8c; padding-bottom: 10px; margin-bottom: 4px; }
        .entete-titre img.logo-agence { max-height: 40px; margin-bottom: 6px; }
        .entete-titre h1 { font-size: 18px; color: #1a3c8c; margin: 0 0 4px 0; text-transform: uppercase; }
        .entete-titre p { font-size: 10px; color: #666; margin: 0; text-transform: uppercase; letter-spacing: 1px; }

        h2.article { font-size: 12px; color: #1a3c8c; background-color: #eef2fb; padding: 6px 10px; margin: 16px 0 8px 0; }
        p.corps { text-align: justify; margin: 0 0 8px 0; }

        .parties-table { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin: 14px -10px; }
        .partie { border: 1px solid #d8dfef; vertical-align: top; width: 50%; }
        .partie-entete { background-color: #1a3c8c; color: #ffffff; font-weight: bold; padding: 6px 10px; font-size: 11px; }
        .partie-corps { padding: 10px; }
        .partie-corps .ligne { margin-bottom: 6px; }
        .partie-corps .label { color: #666; font-size: 9px; text-transform: uppercase; }
        .partie-corps .valeur { font-size: 11px; }

        .bien-box { border: 1px solid #d8dfef; padding: 10px; margin-bottom: 10px; }
        .bien-box .ligne { margin-bottom: 4px; }
        .bien-box .label { color: #666; }

        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .signature-table td { width: 50%; vertical-align: top; padding-top: 40px; border-top: 1px solid #999; font-size: 10px; }

        .pied { margin-top: 24px; text-align: center; font-size: 9px; color: #888; border-top: 1px solid #ddd; padding-top: 8px; }
        .note { margin-top: 12px; font-size: 9px; color: #888; font-style: italic; }
    </style>
</head>
<body>
    <div class="conteneur">
        <div class="entete-titre">
            @if ($reglage->logo && file_exists(public_path($reglage->logo)))
                <img class="logo-agence" src="{{ public_path($reglage->logo) }}" alt="Logo">
            @endif
            <h1>Mandat de gérance immobilière</h1>
            <p>Document contractuel — N&deg; {{ $gerance->numero }}</p>
        </div>

        <p class="corps" style="margin-top: 14px;"><strong>ENTRE LES SOUSSIGNÉS</strong></p>

        <table class="parties-table">
            <tr>
                <td class="partie">
                    <div class="partie-entete">LE MANDANT / PROPRIÉTAIRE</div>
                    <div class="partie-corps">
                        <div class="ligne"><div class="label">Nom / Raison sociale</div><div class="valeur">{{ $gerance->bailleur->nom_complet }}</div></div>
                        <div class="ligne"><div class="label">N&deg; CNI / NINEA / RCCM</div><div class="valeur">{{ $gerance->bailleur->piece_identite_numero ?: ($gerance->bailleur->ninea ?: ($gerance->bailleur->rc ?: '....................')) }}</div></div>
                        <div class="ligne"><div class="label">Adresse</div><div class="valeur">{{ $gerance->bailleur->adresse ?: '....................' }}</div></div>
                        <div class="ligne"><div class="label">Téléphone / Email</div><div class="valeur">{{ $gerance->bailleur->telephone ?: '—' }} / {{ $gerance->bailleur->email ?: '—' }}</div></div>
                    </div>
                </td>
                <td class="partie">
                    <div class="partie-entete">LE MANDATAIRE / GÉRANT</div>
                    <div class="partie-corps">
                        <div class="ligne"><div class="label">Nom / Raison sociale</div><div class="valeur">{{ $reglage->nom_societe ?: config('app.name') }}</div></div>
                        <div class="ligne"><div class="label">N&deg; CNI / NINEA / RCCM</div><div class="valeur">{{ $reglage->ninea ?: '....................' }}</div></div>
                        <div class="ligne"><div class="label">Adresse</div><div class="valeur">{{ $reglage->adresse ?: '....................' }}</div></div>
                        <div class="ligne"><div class="label">Téléphone / Email</div><div class="valeur">{{ $reglage->telephone ?: '—' }} / {{ $reglage->email ?: '—' }}</div></div>
                    </div>
                </td>
            </tr>
        </table>

        <p class="corps">
            Le Mandant confie au Mandataire, qui accepte, la gestion administrative, locative et financière du/des bien(s)
            désigné(s) ci-après, dans les limites du présent mandat et des lois et règlements applicables au Sénégal.
        </p>

        <h2 class="article">ARTICLE 1 &ndash; BIEN(S) CONCERNÉ(S)</h2>
        @foreach ($gerance->biens as $bien)
            @php
                $naturesStandards = ['Appartement', 'Maison', 'Immeuble', 'Local commercial'];
                $nature = $bien->categorie->nom ?? null;
                $natureStandard = $nature && in_array($nature, $naturesStandards) ? $nature : null;
            @endphp
            <div class="bien-box">
                <div class="ligne"><span class="label">Adresse / localisation :</span> {{ $bien->adresse ?: '....................' }}@if ($bien->zone), {{ $bien->zone }}@endif</div>
                <div class="ligne">
                    <span class="label">Nature :</span>
                    @foreach ($naturesStandards as $option)
                        {{ $option === $natureStandard ? '☑' : '☐' }} {{ $option }}&nbsp;&nbsp;
                    @endforeach
                    {{ $natureStandard ? '☐' : '☑' }} Autre : {{ $natureStandard ? '....................' : ($nature ?: '....................') }}
                </div>
                <div class="ligne"><span class="label">Références / titre foncier :</span> {{ $bien->numero }}</div>
                <div class="ligne">
                    <span class="label">Loyer mensuel de référence :</span> {{ number_format($bien->loyer_mensuel ?? 0, 0, ',', ' ') }} FCFA.
                    <span class="label">Dépôt de garantie :</span> selon le contrat de location.
                </div>
            </div>
        @endforeach

        <h2 class="article">ARTICLE 2 &ndash; OBJET ET ÉTENDUE DU MANDAT</h2>
        <p class="corps">
            Le Mandataire est autorisé à : rechercher et sélectionner les locataires ; organiser les visites ; préparer et faire
            signer les contrats de location dans le cadre des pouvoirs reçus ; effectuer les états des lieux ; appeler et
            encaisser les loyers, charges et sommes dues ; délivrer quittances/reçus ; suivre les impayés et adresser les
            relances ; assurer le suivi des réparations et de l'entretien ; représenter le Mandant auprès des locataires,
            fournisseurs et administrations pour les actes de gestion courante ; conserver les pièces justificatives et rendre
            compte de la gestion.
        </p>
        <p class="corps">
            Toute opération exceptionnelle (vente, hypothèque, constitution de sûreté, modification substantielle du bien,
            travaux importants, transaction, engagement supérieur au plafond convenu ou procédure judiciaire nécessitant un
            pouvoir spécial) demeure soumise à l'accord écrit préalable du Mandant, sauf urgence destinée à préserver le bien
            ou la sécurité des personnes.
        </p>

        <h2 class="article">ARTICLE 3 &ndash; ENCAISSEMENT ET REDDITION DES COMPTES</h2>
        <p class="corps">
            Les loyers et autres recettes sont encaissés par le Mandataire pour le compte du Mandant. Après déduction des
            sommes expressément autorisées (commission, dépenses justifiées, taxes/frais convenus), le solde est reversé au
            Mandant au plus tard le .......... de chaque mois, sur le compte indiqué par celui-ci. Un relevé mensuel de gestion
            est remis au Mandant, indiquant notamment loyers appelés, loyers encaissés, arriérés, dépenses, honoraires et solde
            à reverser.
        </p>

        <h2 class="article">ARTICLE 4 &ndash; HONORAIRES ET FRAIS</h2>
        <p class="corps">
            En rémunération de la gestion, le Mandant versera au Mandataire :
            {{ $gerance->frais_gestion_mode === 'pourcentage' ? '☑' : '☐' }} une commission de
            {{ $gerance->frais_gestion_mode === 'pourcentage' ? $gerance->frais_gestion_valeur : '..........' }} % des loyers effectivement encaissés ;
            {{ $gerance->frais_gestion_mode === 'montant_fixe' ? '☑' : '☐' }} un forfait mensuel de
            {{ $gerance->frais_gestion_mode === 'montant_fixe' ? number_format($gerance->frais_gestion_valeur, 0, ',', ' ') : '................' }} FCFA ;
            ☐ autre : ....................
            La TVA est à la charge {{ $gerance->tva_charge === 'agence' ? 'de l\'agence' : 'du Mandant' }}, la taxe à la charge
            {{ $gerance->taxe_charge === 'agence' ? 'de l\'agence' : 'du Mandant' }}, et la TOM à la charge
            {{ $gerance->tom_charge === 'agence' ? 'de l\'agence' : 'du Mandant' }}.
            Les frais exceptionnels engagés pour le compte du Mandant devront, sauf urgence, être préalablement autorisés. Les
            dépenses justifiées sont remboursées selon les modalités convenues entre les parties.
        </p>

        <h2 class="article">ARTICLE 5 &ndash; TRAVAUX, ENTRETIEN ET URGENCES</h2>
        <p class="corps">
            Le Mandataire peut faire réaliser les travaux d'entretien courant jusqu'à concurrence de ................ FCFA par
            intervention, sans autorisation préalable. Au-delà de ce montant, l'accord du Mandant est requis, sauf urgence. En
            cas d'urgence affectant la sécurité, l'intégrité du bien ou la continuité d'un service essentiel, le Mandataire
            peut prendre les mesures conservatoires raisonnables et en informer le Mandant sans délai.
        </p>

        <h2 class="article">ARTICLE 6 &ndash; OBLIGATIONS DU MANDANT</h2>
        <p class="corps">
            Le Mandant déclare être propriétaire ou dûment habilité à donner le présent mandat. Il remet au Mandataire les
            documents nécessaires à la gestion du bien et garantit l'exactitude des informations communiquées. Il demeure
            responsable des obligations qui lui incombent en qualité de propriétaire, notamment celles relatives aux travaux
            lourds, à la conformité et aux charges qui ne sont pas légalement ou contractuellement imputables au locataire.
        </p>

        <h2 class="article">ARTICLE 7 &ndash; OBLIGATIONS DU MANDATAIRE</h2>
        <p class="corps">
            Le Mandataire agit avec diligence, loyauté et dans l'intérêt du Mandant. Il tient une comptabilité ou un suivi
            distinct des opérations de gestion, conserve les justificatifs, informe le Mandant des incidents significatifs et
            ne peut utiliser les fonds encaissés pour son propre compte. Il respecte les obligations professionnelles,
            fiscales et réglementaires applicables à son activité.
        </p>

        <h2 class="article">ARTICLE 8 &ndash; IMPAYÉS ET PROCÉDURES</h2>
        <p class="corps">
            Le Mandataire assure les relances amiables et le suivi des impayés. Toute mise en demeure, résiliation, procédure
            d'expulsion ou action judiciaire est conduite conformément à la législation applicable et, lorsque nécessaire, sur
            instruction ou avec pouvoir spécial du Mandant. Les frais de procédure autorisés sont supportés par le Mandant,
            sous réserve de leur éventuelle récupération auprès du débiteur conformément à la loi.
        </p>

        <h2 class="article">ARTICLE 9 &ndash; DURÉE, RENOUVELLEMENT ET RÉSILIATION</h2>
        <p class="corps">
            Le présent mandat est conclu
            @if ($gerance->date_fin)
                pour une durée de {{ $gerance->date_debut->diffInMonths($gerance->date_fin) }} mois
            @else
                pour une durée de .......... mois
            @endif
            à compter du {{ $gerance->date_debut->format('d/m/Y') }}.
            Il est : ☐ renouvelable par tacite reconduction ☐ renouvelable par accord écrit. Chaque partie peut y mettre fin
            moyennant un préavis de .......... jours, notifié par écrit. En cas de manquement grave non réparé après mise en
            demeure, le mandat peut être résilié conformément au droit applicable. À la fin du mandat, le Mandataire remet les
            clés, documents, états comptables et sommes appartenant au Mandant, après apurement des opérations en cours.
        </p>

        <h2 class="article">ARTICLE 10 &ndash; RESPONSABILITÉ</h2>
        <p class="corps">
            Le Mandataire répond des fautes commises dans l'exécution de sa mission. Il n'est pas responsable des impayés,
            dégradations, sinistres ou actes de tiers lorsqu'ils ne résultent pas d'une faute qui lui est imputable. Le Mandant
            conserve les assurances nécessaires relatives au bien, sauf convention écrite contraire.
        </p>

        <h2 class="article">ARTICLE 11 &ndash; NOTIFICATIONS ET DONNÉES</h2>
        <p class="corps">
            Les parties élisent domicile aux adresses indiquées en tête du présent contrat. Toute notification importante est
            faite par écrit, par remise contre décharge, courrier recommandé, exploit d'huissier ou tout autre moyen permettant
            d'en établir la réception. Les données personnelles recueillies dans le cadre de la gestion sont utilisées
            uniquement pour l'exécution du mandat et les obligations légales applicables.
        </p>

        <h2 class="article">ARTICLE 12 &ndash; DROIT APPLICABLE ET RÈGLEMENT DES LITIGES</h2>
        <p class="corps">
            Le présent mandat est soumis au droit sénégalais et, le cas échéant, aux règles communautaires OHADA applicables.
            Les parties recherchent d'abord une solution amiable. À défaut, le litige est soumis à la juridiction sénégalaise
            territorialement compétente, sous réserve des règles impératives de compétence.
        </p>

        <h2 class="article">ARTICLE 13 &ndash; DISPOSITIONS FINALES</h2>
        <p class="corps">
            Le présent mandat exprime l'accord des parties sur son objet. Toute modification doit être constatée par écrit et
            signée par les parties. Les annexes éventuelles (état des lieux, inventaire, grille tarifaire, mandat spécial,
            relevé bancaire, copie des pièces) font partie intégrante du dossier de gestion.
        </p>

        <p class="corps" style="margin-top: 20px;">
            FAIT À {{ $ville }}, LE {{ $gerance->date_debut->format('d / m / Y') }}
        </p>

        <table class="signature-table">
            <tr>
                <td>LE MANDANT / PROPRIÉTAIRE<br>Signature précédée de la mention « Lu et approuvé »</td>
                <td>LE MANDATAIRE / GÉRANT<br>Signature précédée de la mention « Lu et approuvé »</td>
            </tr>
        </table>

        <p class="note">
            Note d'utilisation : ce document constitue une base contractuelle générée automatiquement à partir des données du
            contrat de gérance {{ $gerance->numero }}. Avant signature, il est recommandé de le faire relire et adapter par un
            avocat, notaire ou juriste au Sénégal, notamment selon le type de bien, le statut du Mandataire, le régime du bail
            et les pouvoirs réellement confiés.
        </p>

        <div class="pied">Mandat de gérance immobilière &bull; Sénégal &mdash; {{ $reglage->nom_societe ?: config('app.name') }}</div>
    </div>
</body>
</html>
