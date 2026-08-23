<?php

namespace Database\Seeders;

use App\Models\Documents\DocumentTemplate;
use App\Models\Documents\DocumentTemplateVersion;
use App\Services\Documents\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed les 3 modèles de documents initiaux demandés par le client, chacun avec sa première
 * version publiée (active). Idempotent : peut être rejoué sans dupliquer les modèles.
 */
class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedModele(
            code: DocumentType::LOCATION_HABITATION,
            nom: "Contrat de location à usage d'habitation",
            categorie: 'LOCATION',
            contenu: $this->contenuLocationHabitation()
        );

        $this->seedModele(
            code: DocumentType::LOCATION_COMMERCIAL,
            nom: 'Contrat de location à usage commercial',
            categorie: 'LOCATION',
            contenu: $this->contenuLocationCommercial()
        );

        $this->seedModele(
            code: DocumentType::MANDAT_GERANCE,
            nom: 'Mandat de gérance',
            categorie: 'MANDAT',
            contenu: $this->contenuMandatGerance()
        );
    }

    protected function seedModele(string $code, string $nom, string $categorie, string $contenu): void
    {
        if (DocumentTemplate::where('code', $code)->exists()) {
            return;
        }

        DB::transaction(function () use ($code, $nom, $categorie, $contenu) {
            $modele = DocumentTemplate::create([
                'code' => $code,
                'name' => $nom,
                'category' => $categorie,
                'description' => 'Modèle initial fourni par le client, importé lors de la mise en place du module Gestion Document.',
                'status' => DocumentTemplate::STATUT_ACTIVE,
            ]);

            $modele->versions()->create([
                'version' => 1,
                'status' => DocumentTemplateVersion::STATUT_ACTIVE,
                'content' => $contenu,
                'notes' => 'Version initiale importée.',
                'published_at' => now(),
            ]);
        });
    }

    protected function contenuLocationHabitation(): string
    {
        return <<<'HTML'
<h1>Contrat de location à usage d'habitation</h1>
<p>Entre les soussignés :</p>
<p><strong>{{ bailleur.nom_complet }}</strong>, ci-après dénommé « le Bailleur », demeurant à {{ bailleur.adresse }}, titulaire de la pièce d'identité n° {{ bailleur.piece_identite_numero }}, joignable au {{ bailleur.telephone }} / {{ bailleur.email }},</p>
<p>ET</p>
<p><strong>{{ locataire.nom_complet }}</strong>, ci-après dénommé « le Locataire », demeurant à {{ locataire.adresse }}, titulaire de la pièce d'identité n° {{ locataire.piece_identite_numero }}, joignable au {{ locataire.telephone }} / {{ locataire.email }},</p>
<p>Il a été convenu et arrêté ce qui suit :</p>

<h2>ARTICLE 1 — DÉSIGNATION DES LIEUX</h2>
<p>Le Bailleur donne à bail au Locataire, qui accepte, un bien à usage exclusif d'habitation désigné : <strong>{{ bien.nom }}</strong>, sis à {{ bien.adresse }}, de type {{ bien.type }}, d'une superficie de {{ bien.superficie }}.</p>

<h2>ARTICLE 2 — DURÉE DU BAIL</h2>
<p>Le présent bail est consenti pour prendre effet à compter du {{ location.date_debut }} et jusqu'au {{ location.date_fin }} (le cas échéant, laissé vierge si le bail est à durée indéterminée). Il pourra être renouvelé par tacite reconduction sauf préavis de résiliation adressé par l'une des parties dans un délai de .................... jours avant l'échéance.</p>

<h2>ARTICLE 3 — DESTINATION DES LIEUX</h2>
<p>Les lieux loués sont destinés à l'habitation exclusive du Locataire et des personnes vivant habituellement à son foyer. Toute autre destination, notamment l'exercice d'une activité commerciale, artisanale ou professionnelle, est formellement interdite sauf accord écrit préalable du Bailleur.</p>

<h2>ARTICLE 4 — LOYER ET CHARGES</h2>
<p>Le présent bail est consenti moyennant un loyer mensuel de <strong>{{ location.loyer }}</strong>, payable d'avance le .................... de chaque mois, auquel s'ajoutent des charges locatives de {{ location.charges }} par mois, représentatives notamment de l'entretien des parties communes et des services collectifs.</p>

<h2>ARTICLE 5 — DÉPÔT DE GARANTIE</h2>
<p>À la signature des présentes, le Locataire verse au Bailleur un dépôt de garantie équivalent à .................... mois de loyer, destiné à couvrir les manquements du Locataire à ses obligations locatives constatés lors de son départ. Ce dépôt sera restitué dans un délai de .................... jours après la restitution des clés, déduction faite des sommes justifiées restant dues.</p>

<h2>ARTICLE 6 — ÉTAT DES LIEUX</h2>
<p>Un état des lieux contradictoire est établi entre les parties à l'entrée et à la sortie du Locataire. À défaut, les lieux sont réputés reçus en bon état de réparations locatives.</p>

<h2>ARTICLE 7 — OBLIGATIONS DU LOCATAIRE</h2>
<p>Le Locataire s'engage à payer le loyer et les charges aux termes convenus, à user paisiblement des lieux loués, à les entretenir en bon état et à effectuer les réparations locatives lui incombant, à ne procéder à aucune transformation des lieux sans l'accord écrit du Bailleur, à souscrire une assurance habitation et à en justifier à première demande, et à ne pas céder son bail ni sous-louer le logement sans autorisation écrite du Bailleur.</p>

<h2>ARTICLE 8 — OBLIGATIONS DU BAILLEUR</h2>
<p>Le Bailleur s'engage à délivrer un logement décent et en bon état d'usage, à assurer au Locataire une jouissance paisible des lieux loués, et à entretenir les locaux en état de servir à l'usage prévu, notamment en effectuant les grosses réparations qui lui incombent légalement.</p>

<h2>ARTICLE 9 — RÉSILIATION</h2>
<p>Le Locataire peut résilier le bail à tout moment moyennant un préavis de .................... jours notifié par écrit. Le Bailleur peut solliciter la résiliation en cas de non-paiement du loyer ou des charges, ou de manquement grave et répété aux obligations du présent contrat, après mise en demeure restée infructueuse, conformément aux dispositions légales applicables.</p>

<h2>ARTICLE 10 — CLAUSE RÉSOLUTOIRE</h2>
<p>À défaut de paiement à son terme d'un seul terme de loyer ou de charges, et un mois après un commandement de payer resté sans effet, le présent bail sera résilié de plein droit si bon semble au Bailleur, sans qu'il soit besoin de remplir aucune formalité judiciaire, le Locataire devant alors quitter les lieux immédiatement.</p>

<h2>ARTICLE 11 — LITIGES</h2>
<p>Le présent contrat est soumis au droit sénégalais. Tout litige relatif à son interprétation ou à son exécution sera, à défaut de règlement amiable, porté devant la juridiction sénégalaise territorialement compétente.</p>

<h2>ARTICLE 12 — ÉLECTION DE DOMICILE</h2>
<p>Pour l'exécution des présentes, les parties élisent domicile aux adresses indiquées en tête du présent contrat.</p>

<p>Fait à {{ agence.adresse }}, le {{ contrat.date }}, en deux exemplaires originaux.</p>

<table>
    <tr>
        <th>Le Bailleur</th>
        <th>Le Locataire</th>
    </tr>
    <tr>
        <td>Signature précédée de la mention « Lu et approuvé »</td>
        <td>Signature précédée de la mention « Lu et approuvé »</td>
    </tr>
</table>

<hr>
<p><em>Document généré via {{ agence.nom_societe }} ({{ agence.telephone }} / {{ agence.email }}) — Référence : {{ contrat.reference }}.</em></p>
HTML;
    }

    protected function contenuLocationCommercial(): string
    {
        return <<<'HTML'
<h1>Contrat de location à usage commercial</h1>
<p>Entre les soussignés :</p>
<p><strong>{{ bailleur.nom_complet }}</strong>, ci-après dénommé « le Bailleur », demeurant à {{ bailleur.adresse }}, immatriculé sous le n° NINEA {{ bailleur.ninea }} / RC {{ bailleur.rc }}, joignable au {{ bailleur.telephone }} / {{ bailleur.email }},</p>
<p>ET</p>
<p><strong>{{ locataire.nom_complet }}</strong>, ci-après dénommé « le Preneur », demeurant/domicilié à {{ locataire.adresse }}, titulaire de la pièce d'identité n° {{ locataire.piece_identite_numero }}, joignable au {{ locataire.telephone }} / {{ locataire.email }},</p>
<p>Il a été convenu et arrêté ce qui suit :</p>

<h2>ARTICLE 1 — DÉSIGNATION DES LOCAUX</h2>
<p>Le Bailleur donne à bail au Preneur, qui accepte, des locaux à usage exclusivement commercial désignés : <strong>{{ bien.nom }}</strong>, sis à {{ bien.adresse }}, de type {{ bien.type }}, d'une superficie de {{ bien.superficie }}.</p>

<h2>ARTICLE 2 — DESTINATION DES LOCAUX</h2>
<p>Les locaux loués sont destinés exclusivement à l'exercice de l'activité commerciale suivante : .................... Toute modification de cette destination devra faire l'objet d'un accord écrit préalable du Bailleur (déspécialisation).</p>

<h2>ARTICLE 3 — DURÉE DU BAIL</h2>
<p>Le présent bail commercial est consenti pour une durée de .................... années, à compter du {{ location.date_debut }} jusqu'au {{ location.date_fin }} (le cas échéant), renouvelable dans les conditions prévues par la réglementation applicable aux baux commerciaux, sauf congé délivré par l'une des parties dans les formes et délais légaux.</p>

<h2>ARTICLE 4 — LOYER</h2>
<p>Le présent bail est consenti moyennant un loyer mensuel hors charges de <strong>{{ location.loyer }}</strong>, payable d'avance le .................... de chaque mois, auquel s'ajoutent des charges locatives de {{ location.charges }} par mois. Le loyer pourra faire l'objet d'une révision périodique selon les modalités convenues entre les parties.</p>

<h2>ARTICLE 5 — DÉPÔT DE GARANTIE</h2>
<p>À la signature des présentes, le Preneur verse au Bailleur un dépôt de garantie équivalent à .................... mois de loyer hors charges, destiné à garantir la bonne exécution des obligations du Preneur. Ce dépôt sera restitué en fin de bail, déduction faite des sommes justifiées restant dues, dans un délai de .................... jours après restitution des locaux.</p>

<h2>ARTICLE 6 — ÉTAT DES LIEUX</h2>
<p>Un état des lieux contradictoire, incluant le cas échéant un inventaire des équipements et installations, est établi entre les parties à l'entrée et à la sortie du Preneur.</p>

<h2>ARTICLE 7 — TRAVAUX, AGENCEMENTS ET ENSEIGNE</h2>
<p>Le Preneur ne pourra réaliser de travaux, transformations ou agencements affectant la structure des locaux sans l'accord écrit préalable du Bailleur. L'installation d'enseignes, panneaux ou signalétiques est soumise à l'accord préalable du Bailleur et au respect de la réglementation en vigueur.</p>

<h2>ARTICLE 8 — OBLIGATIONS DU PRENEUR</h2>
<p>Le Preneur s'engage à payer le loyer et les charges aux termes convenus, à exploiter les locaux personnellement et conformément à leur destination, à les entretenir en bon état, à souscrire une assurance couvrant son activité et le risque locatif et à en justifier à première demande, et à ne pas céder son droit au bail ni sous-louer sans l'accord écrit du Bailleur, sauf dispositions légales impératives contraires.</p>

<h2>ARTICLE 9 — OBLIGATIONS DU BAILLEUR</h2>
<p>Le Bailleur s'engage à délivrer des locaux conformes à leur destination commerciale, à assurer au Preneur une jouissance paisible des locaux loués, et à effectuer les grosses réparations qui lui incombent légalement.</p>

<h2>ARTICLE 10 — CESSION ET SOUS-LOCATION</h2>
<p>Toute cession du droit au bail ou sous-location, totale ou partielle, est soumise à l'accord écrit préalable du Bailleur, sous réserve des dispositions légales impératives applicables en la matière, notamment en cas de cession du fonds de commerce.</p>

<h2>ARTICLE 11 — RÉSILIATION ET CLAUSE RÉSOLUTOIRE</h2>
<p>À défaut de paiement à son terme d'un seul terme de loyer ou de charges, et un mois après un commandement de payer resté sans effet, le présent bail sera résilié de plein droit si bon semble au Bailleur, sans qu'il soit besoin de remplir aucune formalité judiciaire, le Preneur devant alors quitter les lieux immédiatement, sans préjudice de tous dommages et intérêts.</p>

<h2>ARTICLE 12 — LITIGES</h2>
<p>Le présent contrat est soumis au droit sénégalais et, le cas échéant, aux règles communautaires OHADA applicables au bail commercial. Tout litige sera, à défaut de règlement amiable, porté devant la juridiction sénégalaise territorialement compétente.</p>

<h2>ARTICLE 13 — ÉLECTION DE DOMICILE</h2>
<p>Pour l'exécution des présentes, les parties élisent domicile aux adresses indiquées en tête du présent contrat.</p>

<p>Fait à {{ agence.adresse }}, le {{ contrat.date }}, en deux exemplaires originaux.</p>

<table>
    <tr>
        <th>Le Bailleur</th>
        <th>Le Preneur</th>
    </tr>
    <tr>
        <td>Signature précédée de la mention « Lu et approuvé »</td>
        <td>Signature précédée de la mention « Lu et approuvé »</td>
    </tr>
</table>

<hr>
<p><em>Document généré via {{ agence.nom_societe }} ({{ agence.telephone }} / {{ agence.email }}) — Référence : {{ contrat.reference }}.</em></p>
HTML;
    }

    protected function contenuMandatGerance(): string
    {
        return <<<'HTML'
<h1>Mandat de gérance immobilière</h1>
<p>Entre les soussignés :</p>
<p><strong>{{ bailleur.nom_complet }}</strong>, ci-après dénommé « le Mandant », demeurant à {{ bailleur.adresse }}, titulaire de la pièce d'identité / NINEA / RC n° {{ bailleur.piece_identite_numero }}, joignable au {{ bailleur.telephone }} / {{ bailleur.email }},</p>
<p>ET</p>
<p><strong>{{ agence.nom_societe }}</strong>, ci-après dénommé « le Mandataire », immatriculé sous le n° NINEA {{ agence.ninea }}, ayant son siège à {{ agence.adresse }}, joignable au {{ agence.telephone }} / {{ agence.email }},</p>
<p>Le Mandant confie au Mandataire, qui accepte, la gestion administrative, locative et financière du bien désigné ci-après, dans les limites du présent mandat et des lois et règlements applicables au Sénégal.</p>

<h2>ARTICLE 1 — BIEN CONCERNÉ</h2>
<p>Le présent mandat porte sur le bien suivant : <strong>{{ bien.nom }}</strong>, sis à {{ bien.adresse }}, de type {{ bien.type }}.</p>

<h2>ARTICLE 2 — OBJET ET ÉTENDUE DU MANDAT</h2>
<p>Le Mandataire est autorisé à : rechercher et sélectionner les locataires ; organiser les visites ; préparer et faire signer les contrats de location dans le cadre des pouvoirs reçus ; effectuer les états des lieux ; appeler et encaisser les loyers, charges et sommes dues ; délivrer quittances/reçus ; suivre les impayés et adresser les relances ; assurer le suivi des réparations et de l'entretien ; représenter le Mandant auprès des locataires, fournisseurs et administrations pour les actes de gestion courante ; conserver les pièces justificatives et rendre compte de la gestion.</p>
<p>Toute opération exceptionnelle (vente, hypothèque, constitution de sûreté, modification substantielle du bien, travaux importants, transaction, engagement supérieur au plafond convenu ou procédure judiciaire nécessitant un pouvoir spécial) demeure soumise à l'accord écrit préalable du Mandant, sauf urgence destinée à préserver le bien ou la sécurité des personnes.</p>

<h2>ARTICLE 3 — ENCAISSEMENT ET REDDITION DES COMPTES</h2>
<p>Les loyers et autres recettes sont encaissés par le Mandataire pour le compte du Mandant. Après déduction des sommes expressément autorisées (commission, dépenses justifiées, taxes/frais convenus), le solde est reversé au Mandant au plus tard le .................... de chaque mois, sur le compte indiqué par celui-ci. Un relevé mensuel de gestion est remis au Mandant.</p>

<h2>ARTICLE 4 — HONORAIRES ET FRAIS</h2>
<p>En rémunération de la gestion, le Mandant versera au Mandataire des honoraires calculés selon le mode « {{ gerance.frais_gestion_mode }} », pour une valeur de {{ gerance.frais_gestion_valeur }}. La TVA est à la charge de : {{ gerance.tva_charge }}. La taxe est à la charge de : {{ gerance.taxe_charge }}. La TOM est à la charge de : {{ gerance.tom_charge }}. Les frais exceptionnels engagés pour le compte du Mandant devront, sauf urgence, être préalablement autorisés.</p>

<h2>ARTICLE 5 — TRAVAUX, ENTRETIEN ET URGENCES</h2>
<p>Le Mandataire peut faire réaliser les travaux d'entretien courant jusqu'à concurrence de .................... FCFA par intervention, sans autorisation préalable. Au-delà de ce montant, l'accord du Mandant est requis, sauf urgence affectant la sécurité, l'intégrité du bien ou la continuité d'un service essentiel.</p>

<h2>ARTICLE 6 — OBLIGATIONS DU MANDANT</h2>
<p>Le Mandant déclare être propriétaire ou dûment habilité à donner le présent mandat. Il remet au Mandataire les documents nécessaires à la gestion du bien et garantit l'exactitude des informations communiquées.</p>

<h2>ARTICLE 7 — OBLIGATIONS DU MANDATAIRE</h2>
<p>Le Mandataire agit avec diligence, loyauté et dans l'intérêt du Mandant. Il tient un suivi distinct des opérations de gestion, conserve les justificatifs, informe le Mandant des incidents significatifs et ne peut utiliser les fonds encaissés pour son propre compte.</p>

<h2>ARTICLE 8 — IMPAYÉS ET PROCÉDURES</h2>
<p>Le Mandataire assure les relances amiables et le suivi des impayés. Toute mise en demeure, résiliation, procédure d'expulsion ou action judiciaire est conduite conformément à la législation applicable et, lorsque nécessaire, sur instruction ou avec pouvoir spécial du Mandant.</p>

<h2>ARTICLE 9 — DURÉE, RENOUVELLEMENT ET RÉSILIATION</h2>
<p>Le présent mandat est conclu à compter du {{ gerance.date_debut }} et jusqu'au {{ gerance.date_fin }} (le cas échéant, laissé vierge si le mandat est à durée indéterminée). Chaque partie peut y mettre fin moyennant un préavis de .................... jours, notifié par écrit. À la fin du mandat, le Mandataire remet les clés, documents, états comptables et sommes appartenant au Mandant, après apurement des opérations en cours.</p>

<h2>ARTICLE 10 — RESPONSABILITÉ</h2>
<p>Le Mandataire répond des fautes commises dans l'exécution de sa mission. Il n'est pas responsable des impayés, dégradations, sinistres ou actes de tiers lorsqu'ils ne résultent pas d'une faute qui lui est imputable.</p>

<h2>ARTICLE 11 — NOTIFICATIONS ET DONNÉES</h2>
<p>Les parties élisent domicile aux adresses indiquées en tête du présent contrat. Les données personnelles recueillies dans le cadre de la gestion sont utilisées uniquement pour l'exécution du mandat et les obligations légales applicables.</p>

<h2>ARTICLE 12 — DROIT APPLICABLE ET RÈGLEMENT DES LITIGES</h2>
<p>Le présent mandat est soumis au droit sénégalais et, le cas échéant, aux règles communautaires OHADA applicables. À défaut de règlement amiable, le litige est soumis à la juridiction sénégalaise territorialement compétente.</p>

<h2>ARTICLE 13 — DISPOSITIONS FINALES</h2>
<p>Le présent mandat exprime l'accord des parties sur son objet. Toute modification doit être constatée par écrit et signée par les parties.</p>

<p>Fait à {{ agence.adresse }}, le {{ contrat.date }}, en deux exemplaires originaux.</p>

<table>
    <tr>
        <th>Le Mandant / Propriétaire</th>
        <th>Le Mandataire / Gérant</th>
    </tr>
    <tr>
        <td>Signature précédée de la mention « Lu et approuvé »</td>
        <td>Signature précédée de la mention « Lu et approuvé »</td>
    </tr>
</table>

<hr>
<p><em>Document généré via {{ agence.nom_societe }} — Référence : {{ contrat.reference }}. Mandat de gérance n° {{ gerance.reference }}.</em></p>
HTML;
    }
}
