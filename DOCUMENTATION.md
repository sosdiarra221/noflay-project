# immoPlus — Documentation fonctionnelle

Application Laravel 12 de gestion d'agence immobilière : gestion locative (bailleurs, biens, locataires, loyers) et gestion commerciale (prospection, partenaires, agenda). Cette documentation décrit chaque fonctionnalité, module par module.

## Sommaire

- [Architecture générale](#architecture-générale)
- [Authentification, rôles & utilisateurs](#authentification-rôles--utilisateurs)
- [Réglages](#réglages)
- [Départements](#départements)
- [Module Locative](#module-locative)
- [Module Commercial](#module-commercial)

---

## Architecture générale

L'application a un menu principal (dashboard général) puis deux **sous-applications** avec leur propre layout, sidebar et dashboard :

| Espace | Entrée | Layout |
|---|---|---|
| Application principale | `/` | `partials.layouts.master` |
| Gestion Locative | `/locative` | `partials.layouts.master-locative` |
| Gestion Commercial | `/dashboard-commercial` | `partials.layouts.master-commercial` |

Chaque sous-application a ses propres partials `head-css` / `vendor-scripts` / `sidebar` / `header` sous `resources/views/partials/{locative,commercial}/`, ce qui permet aux chemins d'assets de fonctionner correctement sur des routes imbriquées (`asset()` partout).

Toutes les routes applicatives sont protégées par le middleware `auth` et déclarées **avant** la route catch-all (`{any}`) qui sert les pages de démonstration du template restées telles quelles.

---

## Authentification, rôles & utilisateurs

- Connexion : `/connexion` (`AuthController@showLogin` / `login`), déconnexion via `POST /deconnexion`.
- 5 rôles applicatifs (`App\Models\Role`) : **Administrateur**, **Directeur**, **Agent immobilier**, **Comptable**, **Assistant**.
- Les autorisations sont définies dans `AppServiceProvider::boot()` via une matrice `ability => [rôles autorisés]` (Gates Laravel). Un Administrateur passe toujours outre (`Gate::before`).
- Abilities notables : `locative.gerer`, `locative.finances`, `locative.operations-sensibles`, `locative.corbeille`, `locative.suppression-definitive`, `locative.journal`, `locative.documents`, `commercial.gerer`, `commercial.operations-sensibles`.
- **Comptes de démonstration** : un utilisateur est créé par département, avec pour mot de passe par défaut `PWD@demo` et pour email `{departement-slug}@demo.com` (ex. `direction@demo.com`, `commercial@demo.com`). Le rôle applicatif attribué dépend du département (Direction → Administrateur, Finance → Comptable, etc. — voir `database/seeders/UserSeeder.php`).

---

## Réglages

Route : `/reglages` (`ReglageController`).

Page à deux onglets :
- **Général** : nom de la société, logo (upload, stocké dans `public/uploads/logos`), email, téléphone, adresse, site web, devise par défaut (FCFA / EURO / DOLLAR, table `devises`). Ces informations alimentent l'en-tête et le pied de page de **tous les documents PDF** générés par l'application (quittances, reçus, fiches proforma, contrats).
- **SMTP** : type d'envoi (`smtp`, `sendmail`, `mailgun`, `ses`, `postmark`, `resend`, `log`), hôte, port, identifiants, chiffrement, adresse et nom d'expéditeur. Le mot de passe SMTP est stocké chiffré (cast `encrypted`) et n'est écrasé que s'il est renseigné à nouveau.

Un enregistrement unique (`Reglage::courant()`) sert de source de vérité ; il est créé automatiquement avec des valeurs par défaut s'il n'existe pas encore.

---

## Départements

Route : `/departements` (`DepartementController`) — CRUD simple (nom unique + description) utilisé pour organiser les utilisateurs et pour le seeding des comptes de démonstration.

---

## Module Locative

Point d'entrée : `/locative` (`LocativeDashboardController`) — tableau de bord avec KPIs (bailleurs, biens, locataires, loyers en cours, échéances impayées) et sa propre sidebar (« Gestion Locative » dans le menu principal y redirige).

### Bailleurs

Route : `/locative/bailleurs` (`BailleurController`).

- Liste filtrable (recherche nom/téléphone/email, type, statut) avec panneau de filtres en accordéon.
- Numérotation automatique `BLR-{année}-{séquence}`.
- Champs : type (particulier/entreprise), prénom, nom, contact, adresse, **pièce d'identité** (select : CNI, Passeport, Permis de conduire, Carte de séjour), NINEA, coordonnées de paiement, notes, statut.
- Modale « Aperçu rapide » (icône + libellé pour chaque information) et fiche complète (`/locative/bailleurs/{id}`) avec onglets **Vue générale / Gérances / Biens / Documents**.
- Suppression = archivage logique (soft delete) avec motif obligatoire.

### Contrats de gérance

Route : `/locative/gerances` (`ContratGeranceController`).

- Un contrat de gérance lie un **bailleur** à l'agence : type de gérance (gestion locative / vente / les deux), frais de gestion (pourcentage ou montant fixe), répartition TVA/Taxe/TOM (à charge du bailleur ou de l'agence), période, statut (brouillon → en attente de signature → actif → suspendu/expiré/résilié/archivé).
- Formulaire de création (`/locative/gerances/creer`) avec sélection du bailleur via **select à recherche** (Choices.js) et possibilité de créer un bailleur à la volée (modal).
- Toute modification d'un champ financier sur un contrat déjà **actif** exige un motif (`operations-sensibles`) et est tracée dans le journal d'activité.
- Export PDF du contrat (`/locative/gerances/{id}/pdf`).
- Numérotation `GER-{année}-{séquence}`.

### Biens

Route : `/locative/biens` (`BienController`).

- Rattachés à un bailleur et, optionnellement, à un contrat de gérance (l'ajout/édition d'un bien se fait depuis la fiche du contrat de gérance).
- Type d'exploitation **location** (statuts : disponible, réservé, en attente d'entrée, occupé, maintenance, indisponible) ou **vente** (à vendre, visite programmée, offre reçue, compromis, vente en cours, vendu).
- Liste avec filtres (bailleur, type d'exploitation, statut) et une **icône de disponibilité** (✓ vert / ✕ rouge) calculée par `Bien::estDisponible()`.
- Modale « Aperçu rapide » avec toutes les caractéristiques du bien et lien vers son contrat de gérance.
- Numérotation `BIEN-{année}-{séquence}`.

### Locataires

Route : `/locative/locataires` (`LocataireController`).

- Liste filtrable (recherche, type, statut), création/édition via modale, **pièce d'identité en select** (mêmes 4 options que les bailleurs).
- **Dossier locataire complet** (`/locative/locataires/{id}`), inspiré des pages « overview » du template, avec bandeau d'en-tête (avatar, statut, badge de profil payeur) et onglets :
  - **Vue générale** : coordonnées, identité, notes, et un graphique (12 derniers mois) comparant loyers attendus vs payés.
  - **Locations & contrats** : tous les contrats de location du locataire.
  - **Loyers** : toutes ses échéances, avec indicateur payé/partiel/impayé.
  - **Paiements** : historique de ses encaissements, avec accès direct au reçu (aperçu + téléchargement).
  - **Fiches financières** : historique des fiches proforma de loyer générées (voir plus bas) + bouton de génération.
  - **Documents** : pièces jointes du locataire (voir section Documents).
- 4 cartes statistiques : total des loyers dus, total payé, **arriérés dus à l'agence**, taux d'échéances payées à temps.
- **Badge de profil payeur**, calculé sur les arriérés : *Bon payeur* (aucun arriéré), *À surveiller* (arriéré ≤ un mois de loyer), *Mauvais payeur* (arriéré > un mois de loyer).
- Numérotation `LOCT-{année}-{séquence}`.

### Locations & contrats de location

Route : `/locative/locations` (`LocationController`).

- Une **Location** regroupe un ou plusieurs **Contrats de location** (un contrat par bien loué) pour un même locataire — permet de louer plusieurs biens en une seule opération.
- Assistant de création (`/locative/locations/creer`) : choix du locataire, sélection des biens disponibles, conditions par bien (loyer, dépôt de garantie, charges), jour d'échéance mensuel. À la création, les biens passent automatiquement au statut « occupé » et les échéances de loyer des 12 premiers mois sont générées.
- Liste filtrable (locataire — select à recherche, statut du contrat), filtres appliqués automatiquement au changement.
- Fiche `/locative/locations/{id}` : bandeau d'en-tête, cartes statistiques (biens loués, loyer total mensuel, total payé, solde restant dû), et une carte par bien loué (loyer, période, nombre d'échéances, liens vers le contrat et son PDF).
- Fiche contrat individuelle (`/locative/contrats/{id}`) : échéances, paiements, actions d'encaissement et de génération manuelle de loyers, export PDF.

### Loyers (échéances)

Route : `/locative/echeances` (« Loyers » dans le menu — `EcheanceLoyerController`).

- Historique de toutes les échéances de loyer, tous contrats confondus, filtrable par locataire, statut, mois et année.
- Icône payé / partiellement payé / impayé par ligne.
- **Génération manuelle de loyers** : bouton « Générer les loyers » ouvrant une modale (contrat — select à recherche —, année, cases à cocher pour les mois) qui appelle `EcheanceLoyerService::genererLoyersManuel()` ; les échéances déjà existantes ou déjà payées ne sont jamais écrasées.
- **Encaissement direct** depuis la liste (modale « Encaisser » identique à celle de la fiche contrat) : montant, mode de paiement, date, référence, note.
- **Quittance de loyer** : chaque échéance ayant reçu au moins un paiement dispose d'une quittance PDF numérotée `RQL-{année}-{mois}-{séquence}`, consultable **dans l'application** (aperçu intégré en `<iframe>`, sans quitter le logiciel) et téléchargeable.

### Encaissements (historique des paiements)

Route : `/locative/encaissements` (`EncaissementController`).

- Historique de tous les paiements de loyer enregistrés, indépendamment de l'échéance.
- 4 cartes statistiques : total encaissé, nombre de paiements, montant moyen, paiements annulés — recalculées selon les filtres actifs.
- Filtres appliqués **automatiquement** (sans bouton « Filtrer ») : locataire (select à recherche), mode de paiement, une **période rapide** (Aujourd'hui / Hier / Cette semaine / Ce mois / Cette année) qui prend le pas sur un filtre **mois + année combiné** (un seul select, construit dynamiquement à partir des mois réellement présents dans les données, plutôt que deux longs selects séparés).
- Chaque paiement donne accès à son reçu (aperçu intégré + téléchargement PDF).

### Fiches proforma de loyer

Modèle : `FicheLocative`, table `fiches_locatives`.

- Document mensuel envoyé au locataire avant échéance, récapitulant : loyer mensuel, arriérés éventuels, frais d'agence, taxe d'habitation (TOM), TVA (taux configurable, 18 % par défaut) et le **montant total à payer**.
- Générée depuis l'onglet « Fiches financières » du dossier locataire (choix du contrat si le locataire en a plusieurs, mois, année, et override optionnel des frais d'agence / TOM / TVA).
- Numérotée `FP-{année}-{mois}-{séquence}` ; regénérer une fiche pour un mois déjà traité **met à jour** l'enregistrement existant (le numéro ne change pas).
- Les arriérés sont calculés automatiquement : somme des échéances **antérieures** au mois facturé, non payées et non annulées.
- Consultable dans l'application (aperçu `<iframe>`) et téléchargeable en PDF ; **historique conservé** dans le dossier du locataire.

### Documents (bailleurs, locataires, gérances, contrats)

Système polymorphe (`Document`, table `documents`), rattachable à un bailleur, un locataire, un contrat de gérance ou un contrat de location.

- Ajout via une **modale** demandant un **Titre** libre et un **Type** fixe : *Papier Juridique*, *Reçu*, *Papier Administrative*, *Facture*, *Quittance de loyer*.
- Liste avec téléchargement et suppression (protégés par l'ability `locative.documents`).
- Fichiers stockés sur le disque `public` (`storage/app/public/documents`), taille affichée de façon lisible (o / Ko / Mo).

### Reversements

Route : `/locative/reversements` (`ReversementController`) — suivi des sommes reversées aux bailleurs après déduction des frais de gestion.

### Corbeille & Journal d'activité

- **Corbeille** (`/locative/corbeille`, ability `locative.corbeille`) : centralise tous les éléments archivés (soft delete) du module — bailleurs, biens, locataires, contrats — avec restauration ou suppression définitive (`locative.suppression-definitive`).
- **Journal d'activité** (`/locative/journal`, ability `locative.journal`) : audit trail automatique (création / modification / suppression / restauration) alimenté par le trait `Auditable`, présent sur la plupart des modèles du module. Chaque entrée capture l'utilisateur, l'action, les données avant/après, un motif optionnel et l'adresse IP.

### Paramètres Locative

Route : `/locative/parametres` (`ParametreLocativeController`) — gestion des référentiels **Catégories de biens** et **Modes de paiement**, sous forme d'onglets.

### Toutes les dates

Les dates numériques restent au format `jj/mm/aaaa` partout (listes, tableaux). Les dates « narratives » (horodatage d'un événement dans une timeline, en-tête de document PDF) utilisent le format long français via la macro `Carbon::versionLongue()`, ex. *« Dimanche 23 août 2026 »*.

---

## Module Commercial

Point d'entrée : `/dashboard-commercial` (`CommercialDashboardController`) — KPIs (prospects par statut, taux de conversion, prospects sans activité depuis 7 jours) et 3 graphiques ApexCharts (répartition par statut, par source, tendance des 14 derniers jours).

### Prospection

Route : `/commercial/prospects` (`ProspectController`).

- Liste filtrable (recherche, type de demande, source, statut) au format « cms-blog » (accordéon de filtres + tableau).
- Création via modale extra-large : partenaire apporteur (**select à recherche**, en tête de formulaire), prénom/nom (facultatifs), téléphone (obligatoire), email, adresse, type de demande, source (obligatoire), devise, budget min/max, besoin exprimé.
- **Détection de doublon** à la création (téléphone ou email déjà connu) : une alerte propose d'ouvrir la fiche existante ou de créer quand même.
- Modale « Aperçu rapide » avec libellés explicites pour chaque information (téléphone, email, adresse, type de demande, budget, source, besoin).
- Fiche prospect complète (`/commercial/prospects/{id}`, inspirée d'`apps-ecommerce-order-details`) : mini-cartes d'information, historique des activités, **timeline des changements de statut** (dates en format long français), bouton de conversion.
- **Changement de statut** obligatoirement motivé (formulaire dédié ; motif par défaut « Aucune information renseignée » si laissé vide), historisé dans `commercial_status_histories`.
- **Conversion** : un prospect au statut *Gagné* peut être converti en locataire (création automatique dans le module Locative) puis redirigé vers la création d'une location.

### Activités

Rattachées à un prospect (`ActiviteController`) : appel, email, WhatsApp, SMS, visite, rendez-vous, note, relance, document, autre — alimentent la timeline de la fiche prospect et la liste « Activités récentes » du dashboard.

### Partenaires

Route : `/commercial/partenaires` (`PartenaireController`).

- Répertoire des apporteurs d'affaires : agence immobilière, notaire, banque, apporteur d'affaires, autre.
- Champs : nom, contact, téléphone, email, adresse, **commission (%)**, statut, notes.
- Colonne « Prospects apportés » : nombre de prospects liés à ce partenaire.
- Peut être sélectionné comme « partenaire apporteur » lors de la création d'un prospect, permettant de suivre les commissions dues.
- Numérotation `PART-{année}-{séquence}`.

### Agenda

Route : `/commercial/agenda` (`AgendaController`) — calendrier de rendez-vous persistés en base (`commercial_rendez_vous`), basé sur FullCalendar (localisé en français).

- Types de rendez-vous : rendez-vous, visite, appel, autre (couleur dédiée) ; statuts planifié / terminé / annulé.
- Création par clic sur une date ou bouton dédié (modale large avec sélection optionnelle d'un prospect), édition et suppression par clic sur un événement (glisser-déposer pour changer la date pris en charge).
- Un rendez-vous lié à un prospect génère automatiquement une activité correspondante sur sa fiche.

### Rapports

Route : `/commercial/rapports` (`RapportController`).

- Filtrable par plage de dates (par défaut, 30 derniers jours).
- KPIs, répartition par statut / source / type de demande (graphiques), tableau de **performance par commercial** (prospects gérés, gagnés, taux de conversion).
- Export CSV des prospects de la période.

### Paramètres Commercial

Route : `/commercial/parametres` (`ParametreCommercialController`) — gestion des référentiels **Sources** (Facebook, Appel, Site web, etc.) et **Types de demande** (Vente, Location, Estimation, etc.).
