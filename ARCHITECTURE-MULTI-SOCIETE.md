# Faire évoluer Takha CRM vers un logiciel multi-sociétés — étude & recommandations

> Document d'analyse, pas d'implémentation. Objectif : poser clairement le choix d'architecture, expliquer pourquoi, et proposer une feuille de route qui ne casse rien de l'existant.

## 1. Reformulation du besoin

Vous voulez que ce logiciel (aujourd'hui pensé pour **une seule société**) devienne un produit installable par **plusieurs PME clientes**, avec :

1. Vous (éditeur) créez le compte d'une PME et activez son abonnement + les modules qu'elle a souscrits (Locative, RH, Facturation...).
2. La PME se connecte ensuite et gère **elle-même** ses utilisateurs, rôles et permissions via son propre écran Administration — exactement comme aujourd'hui.
3. **Aucune donnée d'une PME ne doit être visible ou mélangée avec celle d'une autre** (ni employés, ni factures, ni journal d'activité...).
4. Les modules d'une même PME peuvent se parler entre eux (ex : Commercial peut lire/écrire dans Facturation), mais **jamais à travers deux PME différentes**.
5. Vous ne voulez pas d'un "SaaS" — sous-entendu : pas d'une base de données unique où tout le monde cohabite avec un simple filtre applicatif.

Ce point 5 est la clé de tout le reste — voir §3.

## 2. Ce qui existe déjà (et qui est réutilisable presque tel quel)

Le projet a, sans le savoir, déjà construit deux des trois briques dont un système multi-sociétés a besoin :

**a) Un système de modules activables/désactivables**, déjà fonctionnel :
- table `modules` (`cle`, `actif`, `ordre`...), modèle `App\Models\Module::estActif()`.
- middleware `EnsureModuleActif` appliqué sur chaque groupe de routes (`->middleware('module.actif:rh')`, etc.).
- sidebar principal qui masque les liens des modules désactivés.
- 7 modules actuels : Locative, Finance, Commercial, Documents, Facturation, RH, Administration.

C'est exactement le mécanisme qu'il faut pour "activer les modules souscrits par une PME" — il ne manque que de le brancher sur un abonnement plutôt que sur un simple bouton on/off (§5).

**b) Un RBAC dynamique par société**, déjà fonctionnel :
- tables `roles`, `permissions`, `permission_role`, gérées depuis Administration > Rôles & permissions.
- `Gate::define()` généré dynamiquement dans `AppServiceProvider::boot()` à partir de la base — donc déjà "par installation", pas codé en dur.
- chaque PME pourra créer ses propres rôles/permissions sans toucher au code.

**c) Ce qui manque totalement : la notion de société/tenant elle-même.**
`users`, `modules`, `permissions`, et toutes les tables métier (`employes`, `bailleurs`, `factures`...) n'ont **aucune colonne** rattachant une ligne à une société. Le logiciel suppose aujourd'hui qu'il n'existe qu'une seule entreprise sur toute l'installation. C'est le vrai chantier.

## 3. Le choix à trancher en premier : qui héberge le serveur ?

Votre phrase « pas un SaaS » cache en réalité **deux questions différentes** qu'il faut séparer, parce qu'elles ont des réponses différentes :

| Question | Ce qui compte vraiment |
|---|---|
| **Où tourne le serveur ?** | Chez vous (vous hébergez pour tous vos clients) ou chez chaque PME (elle installe sur son propre serveur/hébergement) ? |
| **Comment les données sont-elles isolées ?** | Une seule base partagée avec un filtre applicatif (`societe_id` sur chaque ligne) — ou une base physiquement séparée par société ? |

Ces deux axes sont indépendants. "SaaS" au sens strict ne désigne que le premier axe (hébergement centralisé) ; ce que vous redoutez ("les données se mélangent") est en réalité un problème du **deuxième axe** (isolation logique vs physique). On peut très bien héberger soi-même (comme un SaaS) tout en gardant une base de données strictement séparée par client (comme un logiciel installé). C'est d'ailleurs ce que votre description implique déjà : « moi, administrateur principal, je crée le compte de la PME » suppose un endroit central où vous agissez — donc un minimum d'hébergement/contrôle centralisé, même léger.

Trois modèles possibles, du plus mutualisé au plus isolé :

**Modèle A — Base unique partagée, filtrage par `societe_id`** (SaaS classique "multi-tenant mutualisé")
Une seule base MySQL, chaque table métier reçoit une colonne `societe_id`, un scope global Eloquent l'ajoute automatiquement à chaque requête. Le moins cher à opérer, mais **le risque que vous redoutez existe réellement** : un scope oublié sur une seule requête (migration future, requête brute, `DB::table(...)` sans le scope) et deux sociétés voient leurs données se mélanger. Sur un projet de cette taille (93k+ symboles, des dizaines de contrôleurs), le risque d'oubli n'est pas négligeable dans la durée. **Écarté**, vu votre exigence explicite.

**Modèle B — Une base de données par société ("silo"), hébergement centralisé chez vous**
Toujours une seule installation du code, mais chaque PME a sa **propre base MySQL** (`societe_12_db`, `societe_13_db`...). Au login, le système sait à quelle société l'utilisateur appartient et bascule la connexion vers la bonne base. **Isolation physique réelle** — une requête mal écrite dans un module ne peut techniquement pas lire les données d'une autre société, puisqu'elle n'a pas la connexion ouverte vers cette base. Vous gardez un contrôle central (créer le compte, activer les modules, suspendre un impayé) sans jamais héberger les données de deux clients dans les mêmes tables.

**Modèle C — Une installation totalement indépendante par PME**
Chaque PME installe le logiciel sur son propre serveur (ou un hébergement que vous lui fournissez, mais dédié à elle seule) : son propre code, sa propre base, aucune connexion réseau vers vous. Isolation maximale, mais vous perdez tout contrôle centralisé permanent — « activer son compte » ne peut alors se faire qu'au moment du déploiement initial (ou via un système de licence à part, ex. une clé qui expire, vérifiée périodiquement).

### Recommandation

**Modèle B (silo par société, hébergement centralisé)** correspond le mieux à ce que vous décrivez : vous gardez la main (créer/activer un compte, gérer l'abonnement) tout en respectant l'exigence "aucun mélange". C'est un modèle standard, mature, et bien outillé en Laravel — pas une expérimentation.

Le package de référence est **[stancl/tenancy](https://tenancyforlaravel.com/)** :
- gère nativement le "silo" par société (une base par tenant), avec bascule automatique de connexion selon le sous-domaine ou l'identifiant de société.
- fournit des commandes pour migrer/seeder/exécuter une tâche planifiée **sur tous les tenants d'un coup** (`tenants:migrate`, `tenants:run`) — utile pour vos commandes déjà existantes comme `rh:incrementer-soldes-conges`.
- gère aussi le stockage fichiers isolé par tenant (photos employés, contrats PDF, justificatifs d'absence...) — pertinent puisque ce projet stocke déjà beaucoup de fichiers (`storage/app/public/employes`, `contrats-travail`, `absences`...).
- compatible Laravel 12, activement maintenu.

Modèle C reste pertinent si votre intention commerciale est réellement "vendre une licence, le client héberge chez lui" plutôt que "gérer un parc de clients hébergés par moi" — c'est une question commerciale autant que technique, à trancher avant de coder quoi que ce soit.

## 4. Ce qui devient "central" (vous) vs ce qui reste "par société" (la PME)

| Central (une seule fois, chez vous) | Par société (dupliqué pour chaque PME) |
|---|---|
| Registre des sociétés clientes (raison sociale, statut du compte, date de création) | Tout ce qui existe déjà : `users`, `roles`, `permissions`, `modules`, et toutes les tables métier |
| Abonnement / plan souscrit par société, et **quels modules elle a le droit d'activer** | L'écran Administration existant (utilisateurs, rôles, permissions, modules) — géré par la PME elle-même, sans y toucher |
| Écran "vendeur" : créer une société, activer/suspendre son compte, choisir son plan | Toutes les données métier (Locative, RH, Facturation, Commercial...) |
| Éventuelle facturation de VOUS vers VOS clients (votre propre business model) — à ne pas confondre avec le module Facturation métier qui existe déjà pour la clientèle finale des PME |

Le module `Facturation` déjà construit dans ce projet sert une PME à facturer **ses propres clients**. Si vous facturez vous-même vos clients PME pour leur abonnement, c'est un **système séparé**, au niveau central — ne pas réutiliser le module métier existant pour ça, les deux notions de "client" et de "facture" n'ont rien à voir.

## 5. Brancher l'existant sur ce modèle, sans le réécrire

Bonne nouvelle : avec le modèle B, chaque société a sa propre base — donc sa propre table `modules`, sa propre table `permissions`, etc. **Le code applicatif de chaque module ne change quasiment pas.** Deux ajouts seulement :

1. **Contrainte "abonnement" au-dessus du toggle existant.** Aujourd'hui n'importe quel administrateur peut activer/désactiver un module librement (`administration.modules.toggle`). Avec un abonnement, il faut que la liste des modules *proposés* à l'écran "Modules" d'une PME se limite à ceux que son plan autorise — le toggle actuel devient "activer/désactiver parmi ce qui est souscrit", pas "activer n'importe quoi". C'est une vérification supplémentaire dans `ModuleController`, pas une réécriture.
2. **Le contexte tenant doit être résolu tôt** (middleware, avant tout le reste) pour que la connexion DB et le disque de stockage soient corrects dès la première requête — c'est exactement le rôle de `stancl/tenancy`, pas quelque chose à construire à la main.

## 6. Communication entre modules d'une même société

Puisque tous les modules d'une même PME vivent dans **la même base** (modèle silo, pas de séparation par module), la communication inter-module reste aussi simple qu'aujourd'hui. Le projet le fait déjà bien : `Facturation\Client::depuisProspect()` relie proprement Commercial et Facturation via une relation Eloquent classique, sans dupliquer les données client.

Deux précautions à prendre en généralisant ce pattern à d'autres modules, pour que la désactivation d'un module par une PME ne casse jamais un autre module encore actif :

- **Ne jamais faire dépendre un module de la présence d'un autre sans vérifier `Module::estActif()` d'abord.** Exemple concret : si une PME n'a pas souscrit Facturation, l'écran Commercial ne doit montrer aucun lien "convertir en facture" — le sidebar principal fait déjà ça pour les liens de menu, il faut généraliser le réflexe aux actions internes aux pages (boutons, widgets croisés).
- **Garder les écritures cross-module à travers des méthodes explicites du modèle** (comme `Client::depuisProspect()`), pas des requêtes directes dispersées dans des contrôleurs d'un autre module — ça limite le risque qu'un module cassé par une désactivation entraîne une erreur 500 ailleurs.

Rien de tout ça ne casse avec le passage au multi-société : c'est une bonne pratique interne au module, indépendante de l'isolation entre sociétés.

## 7. Feuille de route proposée (progressive, rien de cassant)

1. **Introduire `stancl/tenancy`** et le modèle `Tenant` central, sans toucher au code métier existant.
2. **Déplacer les migrations actuelles** vers `database/migrations/tenant/` (mécanisme natif du package) — chaque société reçoit sa propre base à sa création, avec le même schéma qu'aujourd'hui.
3. **Ajouter la table centrale des abonnements** (société ↔ modules souscrits) et faire respecter cette liste par `ModuleController` avant de proposer un toggle.
4. **Construire l'écran central "vendeur"** (vous) : créer une société, choisir son plan/ses modules, activer/suspendre le compte. C'est un tout petit espace d'administration séparé de l'application PME.
5. **Adapter les tâches planifiées existantes** (`rh:incrementer-soldes-conges`, `notifications:verifier-evenements-planifies`) pour tourner par société via `tenants:run` plutôt qu'une seule fois globalement.
6. **Vérifier le stockage fichiers** : les chemins actuels (`storage/app/public/employes`, `contrats-travail`, `absences`, `documents`...) doivent devenir tenant-aware — le package gère ça, mais il faut vérifier chaque `Storage::disk('public')->store(...)` existant.
7. Ne toucher à **rien** dans l'écran Administration existant de la PME (utilisateurs/rôles/permissions/modules) — il continue de fonctionner exactement pareil, juste "à l'intérieur" de la base de la société connectée.

## 8. Points de vigilance

- **Opérationnel** : une base par société veut dire une migration à exécuter par société à chaque déploiement — `stancl/tenancy` l'automatise (`tenants:migrate`), mais c'est une réalité à anticiper (temps, monitoring des échecs partiels).
- **Sauvegardes** : sauvegarder N bases au lieu d'une — à prévoir dans la stratégie d'hébergement, pas dans le code.
- **Ne pas sur-ingénierer si le nombre de clients visés reste petit** (quelques PME) : si votre intention réelle est plutôt "chaque client héberge chez lui, je n'ai pas de contrôle central permanent", le modèle C (installation indépendante + licence simple) est nettement plus simple à construire et suffit largement. C'est une décision commerciale à trancher avant la décision technique — le modèle B ne se justifie que si vous comptez héberger et gérer plusieurs sociétés vous-même dans la durée.

## 9. Application concrète à votre hébergement (o2switch + takha.com)

Vous hébergez vous-même chez o2switch, sur `takha.com`, avec un sous-domaine par PME (`pme1.takha.com`, `pme2.takha.com`...) et une base par PME. C'est exactement le **Modèle B** recommandé au §3, et o2switch est bien équipé pour ça — vérifié point par point :

**Sous-domaine wildcard (`*.takha.com`).** cPanel accepte de créer littéralement un sous-domaine dont le nom est `*` (outil "Sous-domaines"), pointé vers le même dossier `public/` que votre application Laravel. Toutes les requêtes vers n'importe quel `xxx.takha.com` atterrissent donc sur la même installation, et c'est le code (via `stancl/tenancy`, identification par domaine) qui détermine quelle société correspond au sous-domaine demandé et bascule la connexion vers sa base.

**SSL wildcard.** L'outil Let's Encrypt du cPanel o2switch propose une case "Include Wildcard *" avec validation par DNS — un seul certificat couvre `takha.com` et tous ses sous-domaines de premier niveau, renouvelé automatiquement par AutoSSL. Pas besoin d'un certificat par PME.

**Une base par PME.** L'offre o2switch annonce un nombre illimité de bases MySQL et d'utilisateurs MySQL associés, et leur propre documentation recommande explicitly le schéma "1 base + 1 utilisateur MySQL dédié" par site/script hébergé — c'est très exactement le modèle silo. Aucune limite de plan à surveiller de ce côté.

**⚠️ Point de vigilance réel : la création automatique des bases.** `stancl/tenancy` crée normalement une base + un utilisateur MySQL **automatiquement** à chaque nouvelle PME, via une connexion MySQL qui doit avoir le droit `CREATE DATABASE` / `CREATE USER`. Sur un hébergement mutualisé cPanel (o2switch comme la plupart des mutualisés), l'utilisateur MySQL fourni **n'a en général pas ce droit global** — seul le cPanel (ou son API) peut créer des bases et des utilisateurs, avec un préfixe imposé (`cpaneluser_nomdebase`). C'est une limitation courante de l'hébergement mutualisé, pas spécifique à o2switch, mais elle casserait le fonctionnement "prêt à l'emploi" du package tel quel. Trois options, à valider avant de s'engager :

1. **Brancher la création de base sur l'API cPanel/UAPI d'o2switch** plutôt que sur un `CREATE DATABASE` SQL direct — `stancl/tenancy` permet d'écrire son propre "database manager", donc c'est faisable, mais c'est du code à écrire spécifiquement pour cPanel, pas fourni tel quel par le package.
2. **Pré-créer un pool de bases vides** dans cPanel à l'avance (ex. `takha_tenant_01`, `takha_tenant_02`...) et les attribuer aux PME au fur et à mesure des inscriptions — solution manuelle mais simple, viable si le rythme de création de comptes reste modéré.
3. **Passer sur une offre Cloud/VPS o2switch** (ils en proposent, à côté de leur offre mutualisée classique) pour avoir un utilisateur MySQL avec les pleins droits — alors `stancl/tenancy` fonctionne tel quel, sans contournement.

À vérifier concrètement avant de choisir : tester si votre compte o2switch actuel autorise `CREATE DATABASE`/`CREATE USER` depuis une connexion applicative, ou contacter leur support pour confirmer.

## 10. Inventaire technique — ce qui devra être vérifié au moment de la bascule

GitNexus n'a pas pu produire un index à jour dans cet environnement (les workers du parseur plantent en boucle sur ce poste — deux tentatives, deux échecs identiques ; ce n'est pas lié au code du projet). L'inventaire ci-dessous vient donc d'une exploration directe du code, volontairement ciblée sur ce qui compte réellement pour la faisabilité du passage au multi-société — pas un audit exhaustif des 71 contrôleurs / 55 modèles / 298 vues du projet.

**Taille actuelle du projet** (pour calibrer l'effort) : 71 contrôleurs, 55 modèles, 85 migrations, 298 vues Blade.

**Fichiers stockant des fichiers uploadés** (photos, contrats, justificatifs) — chacun devra utiliser un disque tenant-aware plutôt que `storage/app/public` global :
- `EmployeController`, `EmployeDocumentController`, `ContratTravailController`, `AbsenceController` (module RH — photos, documents contractuels, justificatifs d'absence)
- `Locative\DocumentController` (documents attachés aux bailleurs/locataires/contrats)
- `ProfilController` (photo de profil utilisateur)

Aucune surprise ici — `stancl/tenancy` fournit un disque `public` automatiquement réécrit par tenant, ces contrôleurs n'ont pas à changer de logique, juste à continuer d'utiliser le disque `public` par défaut (le package se charge de le faire pointer au bon endroit selon le tenant courant).

**Requêtes `DB::table()` directes** (contournent Eloquent, à vérifier qu'elles suivent bien la connexion du tenant courant plutôt que la connexion par défaut de l'app) : `AffectationController`, `SiteController`, `VerifierEvenementsPlanifies`. Avec `stancl/tenancy`, la connexion par défaut est réécrite dynamiquement, donc ces appels suivront automatiquement le tenant — à confirmer une fois le package en place plutôt qu'à corriger par avance.

**Tâches planifiées existantes**, à faire tourner par société plutôt qu'une seule fois globalement (`tenants:run` au lieu de `Schedule::command` simple) :
- `notifications:verifier-evenements-planifies` (quotidien, 07h00)
- `rh:incrementer-soldes-conges` (mensuel, le 1er)

**`APP_URL` codé en dur dans la config** (`config/app.php`, `config/filesystems.php`) : avec un sous-domaine par société, les liens générés (emails, notifications, URLs de fichiers) ne peuvent plus dépendre d'une seule URL fixe en `.env` — c'est un point que `stancl/tenancy` gère nativement pour les requêtes web (il connaît le domaine courant), mais qui demande une attention particulière pour tout ce qui s'exécute **hors requête HTTP** : les deux commandes planifiées ci-dessus génèrent des liens dans leurs notifications (`route('locative.echeances.index', ...)` etc.) — en exécution CLI, il faudra explicitement indiquer le tenant courant à `stancl/tenancy` avant d'appeler `route()`, sans quoi le lien généré pointerait vers le mauvais sous-domaine ou vers `takha.com` tout court.

## 11. Ce que je n'ai pas fait (au moment de l'étude initiale)

Comme demandé à l'origine, ce document était d'abord une étude, pas une implémentation : aucune migration, aucun package installé, aucun fichier de code modifié. **Ceci a changé — voir §12.**

## 12. Phase 1 — État de l'implémentation (démarrée le 2026-08-26)

Sur demande explicite ("démarre l'implémentation"), la fondation multi-sociétés a été posée avec `stancl/tenancy` v3.10.1, en suivant le modèle "silo" recommandé au §3 (une base MySQL + un sous-domaine par PME). Aucune donnée existante n'a été touchée : une sauvegarde complète (`storage/backups/immo_db_avant_multitenant_20260826_024456.sql`) a été prise avant toute modification, et `immo_db` est simplement devenue la base du premier tenant (`demo`), sans réexport ni migration de données.

**Fait et vérifié :**
- Package installé, `config/tenancy.php` configuré (connexion centrale `central` → base `takha_central`, domaines centraux `takha.test`/`localhost`/`127.0.0.1`).
- `app/Models/Tenant.php` créé (colonnes maison `nom_pme`, `statut`, `plan`).
- Les 85 migrations existantes déplacées vers `database/migrations/tenant/` (déplacement pur, contenu inchangé — sauf un correctif, voir plus bas).
- Routage scindé : `routes/web.php` (minimal, domaine central) / `routes/tenant.php` (intégralité de l'ancien `routes/web.php`, enveloppée dans le middleware `InitializeTenancyByDomain` + `PreventAccessFromCentralDomains`).
- Tenant `demo` créé, pointant explicitement sur `immo_db` (via `setInternal('db_name', 'immo_db')` + `saveQuietly()`, pour ne pas déclencher une création de base sur une base déjà existante) — domaine `demo.takha.test`.
- Tenant `test2` créé avec une base neuve, entièrement provisionnée par le flux automatique du package (`CreateDatabase` + `MigrateDatabase`), domaine `test2.takha.test`.
- **Isolation vérifiée** : `demo` voit 10 employés / 7 utilisateurs (données réelles d'`immo_db`) ; `test2` voit 0/0 (base neuve et vide). Confirmé aussi bien au niveau connexion Eloquent (`Tenant::run()`) qu'au niveau HTTP (`curl` avec en-tête `Host` simulé, sans dépendre du fichier hosts) : page de connexion accessible et correcte sur les deux sous-domaines, redirection d'authentification fonctionnelle et tenant-aware (l'URL de redirection contient bien le bon sous-domaine).
- **Domaine central bloqué** : une requête sur `takha.test` renvoie 404 via `PreventAccessFromCentralDomains`, conforme à l'objectif "pas d'accès aux données depuis le domaine central".
- **Bug corrigé au passage** : la migration `2026_08_24_000003_create_reversements_bailleurs_table.php` généraient un nom d'index unique de plus de 64 caractères (limite MySQL) — jamais un problème sur `immo_db` (construite progressivement, cette migration y était déjà marquée comme jouée), mais bloquant sur toute nouvelle base tenant. Corrigé avec un nom d'index explicite et court.

**Point d'attention noté pour la suite (pas bloquant pour cette phase)** : `routes/web.php` et `routes/tenant.php` définissent tous les deux une route `/`. Comme aucune des deux n'a de contrainte de domaine Laravel (`Route::domain()`), la collection de routes ne garde que la dernière enregistrée (`tenant.php`, chargée plus tard par le `ServiceProvider`) — la route `/` de `web.php` est donc actuellement invisible au routeur. Résultat observé : `takha.test/` renvoie bien un 404 (le comportement voulu), mais par effet de bord du blocage tenancy plutôt que par un vrai rendu de la page centrale. Sans conséquence tant que l'écran "vendeur" central n'existe pas encore (hors scope de cette phase) ; à traiter via des chemins distincts ou un `Route::domain()` explicite quand cet écran sera construit.

**Reste à faire pour clore la Phase 1 :**
1. Ajouter au fichier hosts Windows (`C:\Windows\System32\drivers\etc\hosts`, en tant qu'administrateur) — modification système que je ne fais pas moi-même :
   ```
   127.0.0.1 takha.test
   127.0.0.1 demo.takha.test
   ```
2. Vérification finale dans un vrai navigateur une fois ces lignes ajoutées (`php artisan serve` tourne déjà en arrière-plan sur le port 8000).
3. Décider du commit — rien n'a encore été committé pour ce chantier (package, config, modèle `Tenant`, scission des routes, correctif de migration).

Tout ce qui était listé au §8 comme "hors scope pour cette phase" (écran vendeur complet, respect des abonnements par module, adaptation des tâches planifiées via `tenants:run`, contournement `CREATE DATABASE` chez o2switch, DNS wildcard réel) reste effectivement reporté à une itération suivante.
