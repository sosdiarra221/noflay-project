<?php

namespace Database\Seeders;

use App\Models\Bailleur;
use App\Models\Bien;
use App\Models\CategorieBien;
use App\Models\CategorieDepense;
use App\Models\Caution;
use App\Models\ChargeLocative;
use App\Models\ContratGerance;
use App\Models\ContratLocation;
use App\Models\DepenseLocation;
use App\Models\EcheanceLoyer;
use App\Models\Locataire;
use App\Models\Location;
use App\Models\ModePaiement;
use App\Models\Paiement;
use App\Models\ReversementBailleur;
use App\Models\User;
use App\Models\VersementBailleur;
use App\Services\Documents\DocumentGenerationService;
use App\Services\Finance\VentilationService;
use App\Services\Locative\EcheanceLoyerService;
use App\Services\Locative\NumeroService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Simulation logique complète du module Locative/Finance : 10 bailleurs sous mandat de gérance
 * avec un nombre de biens variable (1 à 4), des locataires (dont certains cumulent plusieurs
 * contrats de location distincts), des loyers courant uniquement de janvier à août 2026, un mix
 * volontaire de locataires "bons payeurs" et de locataires en arriéré, des versements aux
 * bailleurs majoritairement partiels (arriéré agence -> bailleur), des dépenses de location et
 * des cautions dans des états variés.
 *
 * Remet d'abord à zéro toutes les tables "métier" (tout sauf les utilisateurs et les tables de
 * configuration/référence indispensables au fonctionnement de l'application : rôles, permissions,
 * devises, départements, réglages, catégories, modes de paiement, modèles de documents).
 */
class SimulationLocativeSeeder extends Seeder
{
    protected DocumentGenerationService $documents;

    protected EcheanceLoyerService $echeances;

    protected VentilationService $ventilation;

    protected array $modesPaiementIds = [];

    protected int $acteurId;

    public function run(): void
    {
        $this->documents = app(DocumentGenerationService::class);
        $this->echeances = app(EcheanceLoyerService::class);
        $this->ventilation = app(VentilationService::class);
        $this->modesPaiementIds = ModePaiement::pluck('id')->all();
        $this->acteurId = User::where('name', 'Direction')->value('id') ?? User::query()->value('id');

        $this->resetDonneesMetier();

        $bailleursData = $this->creerBailleursEtGerances();
        $bailleursData = $this->creerBiens($bailleursData);
        $locataires = $this->creerLocataires();
        $contrats = $this->creerLocationsEtContrats($bailleursData, $locataires);
        $this->simulerEncaissements($contrats);
        $this->simulerDepenses($bailleursData, $contrats);
        $this->simulerVersements($bailleursData);

        $this->command?->info('Simulation locative générée : '.count($bailleursData).' bailleurs, '.collect($bailleursData)->sum(fn ($b) => count($b['biens'])).' biens, '.count($locataires).' locataires, '.count($contrats).' contrats de location.');
    }

    /**
     * Vide toutes les tables transactionnelles (bailleurs, biens, locations, paiements, cautions,
     * dépenses, reversements, prospection commerciale, journal d'activité, notifications...) en
     * préservant les utilisateurs et les tables de configuration/référence.
     */
    protected function resetDonneesMetier(): void
    {
        $tables = [
            'notifications',
            'charges_locatives',
            'versements_bailleurs',
            'cautions',
            'depenses_location',
            'reversements_bailleurs',
            'fiches_locatives',
            'document_revisions',
            'documents_generes',
            'documents',
            'paiements',
            'echeances_loyer',
            'contrats_location',
            'locations',
            'contrats_gerance',
            'biens',
            'locataires',
            'bailleurs',
            'commercial_rendez_vous',
            'commercial_partenaires',
            'commercial_status_histories',
            'commercial_activites',
            'prospects',
            'journaux_activite',
        ];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * @return array<int, array{bailleur: Bailleur, gerance: ContratGerance, nbBiens: int, biens: array}>
     */
    protected function creerBailleursEtGerances(): array
    {
        $bailleursData = [
            ['type' => 'particulier', 'prenom' => 'Mamadou', 'nom' => 'Fall', 'telephone' => '771000001', 'nbBiens' => 4],
            ['type' => 'particulier', 'prenom' => 'Aïssatou', 'nom' => 'Diallo', 'telephone' => '771000002', 'nbBiens' => 2],
            ['type' => 'entreprise', 'nom' => 'Teranga Immobilier SARL', 'telephone' => '338000003', 'nbBiens' => 3],
            ['type' => 'particulier', 'prenom' => 'Ousmane', 'nom' => 'Sarr', 'telephone' => '771000004', 'nbBiens' => 2],
            ['type' => 'particulier', 'prenom' => 'Bineta', 'nom' => 'Ndoye', 'telephone' => '771000005', 'nbBiens' => 3],
            ['type' => 'entreprise', 'nom' => 'Baobab Foncier & Gestion', 'telephone' => '338000006', 'nbBiens' => 4],
            ['type' => 'particulier', 'prenom' => 'Modou', 'nom' => 'Diop', 'telephone' => '771000007', 'nbBiens' => 2],
            ['type' => 'particulier', 'prenom' => 'Astou', 'nom' => 'Mbaye', 'telephone' => '771000008', 'nbBiens' => 1],
            ['type' => 'entreprise', 'nom' => 'Sénégal Patrimoine Immo', 'telephone' => '338000009', 'nbBiens' => 3],
            ['type' => 'particulier', 'prenom' => 'Cheikhouna', 'nom' => 'Niang', 'telephone' => '771000010', 'nbBiens' => 2],
        ];

        // Cas minoritaires (2 sur 10) où le bailleur assume lui-même la TVA/Taxe/TOM au lieu de
        // l'agence — le reste (majorité) est géré par l'agence.
        $indexChargeBailleur = [2, 7];

        $resultat = [];

        foreach ($bailleursData as $i => $data) {
            $nbBiens = $data['nbBiens'];
            unset($data['nbBiens']);

            $bailleur = Bailleur::create($data + [
                'numero' => NumeroService::genererNumero(Bailleur::class, 'BLR'),
                'adresse' => 'Dakar, Sénégal',
                'statut' => 'actif',
            ]);

            $chargeeParBailleur = in_array($i, $indexChargeBailleur, true);
            $fraisGestionValeur = [8, 10, 10, 10, 12, 10, 10, 9, 10, 11][$i];

            $gerance = ContratGerance::create([
                'numero' => NumeroService::genererNumero(ContratGerance::class, 'GER'),
                'bailleur_id' => $bailleur->id,
                'date_debut' => Carbon::create(2025, 11, 1)->addDays($i * 3),
                'type_gerance' => 'gestion_locative',
                'frais_gestion_mode' => 'pourcentage',
                'frais_gestion_valeur' => $fraisGestionValeur,
                'tva_charge' => $chargeeParBailleur ? 'bailleur' : 'agence',
                'taxe_charge' => $chargeeParBailleur ? 'bailleur' : 'agence',
                'tom_charge' => $chargeeParBailleur ? 'bailleur' : 'agence',
                'statut' => 'actif',
                'notes' => 'Mandat de gérance — simulation logique.',
            ]);

            $resultat[] = ['bailleur' => $bailleur, 'gerance' => $gerance, 'nbBiens' => $nbBiens, 'biens' => []];
        }

        return $resultat;
    }

    /**
     * Catalogue de types de biens avec leur usage (habitation/commercial) et une fourchette de
     * loyer réaliste, utilisé pour répartir des biens variés entre les 10 bailleurs.
     */
    protected function catalogueBiens(): array
    {
        return [
            ['categorie' => 'Studio', 'usage' => 'habitation', 'min' => 75000, 'max' => 100000],
            ['categorie' => 'Appartement', 'usage' => 'habitation', 'min' => 120000, 'max' => 220000],
            ['categorie' => 'Villa', 'usage' => 'habitation', 'min' => 300000, 'max' => 500000],
            ['categorie' => 'Maison', 'usage' => 'habitation', 'min' => 150000, 'max' => 260000],
            ['categorie' => 'Chambre', 'usage' => 'habitation', 'min' => 45000, 'max' => 70000],
            ['categorie' => 'Local commercial', 'usage' => 'commercial', 'min' => 175000, 'max' => 350000],
            ['categorie' => 'Bureau', 'usage' => 'commercial', 'min' => 150000, 'max' => 280000],
            ['categorie' => 'Boutique', 'usage' => 'commercial', 'min' => 100000, 'max' => 190000],
        ];
    }

    protected function creerBiens(array $bailleursData): array
    {
        $catalogue = $this->catalogueBiens();
        $zones = ['Plateau', 'Mermoz', 'Sacré-Cœur', 'Almadies', 'Yoff', 'Ouakam', 'Point E', 'Liberté 6', 'Ngor', 'Sicap Baobab', 'Grand Yoff', 'Parcelles Assainies'];
        $categoriesCache = [];
        $curseurCatalogue = 0;
        $curseurZone = 0;

        foreach ($bailleursData as &$entree) {
            for ($j = 0; $j < $entree['nbBiens']; $j++) {
                $def = $catalogue[$curseurCatalogue % count($catalogue)];
                $curseurCatalogue++;

                if (! isset($categoriesCache[$def['categorie']])) {
                    $categoriesCache[$def['categorie']] = CategorieBien::firstOrCreate(['nom' => $def['categorie']], ['actif' => true])->id;
                }

                $loyer = $def['min'] + (($def['max'] - $def['min']) * (($curseurCatalogue % 5) / 4));

                $bien = Bien::create([
                    'numero' => NumeroService::genererNumero(Bien::class, 'BIEN'),
                    'titre' => $def['categorie'].' — '.$entree['bailleur']->nom_complet,
                    'categorie_bien_id' => $categoriesCache[$def['categorie']],
                    'adresse' => 'Dakar, Sénégal',
                    'zone' => $zones[$curseurZone % count($zones)],
                    'bailleur_id' => $entree['bailleur']->id,
                    'gerance_id' => $entree['gerance']->id,
                    'type_exploitation' => 'location',
                    'loyer_mensuel' => round($loyer, -3),
                    'statut' => 'disponible',
                ]);
                $curseurZone++;

                $entree['biens'][] = ['bien' => $bien, 'usage' => $def['usage']];
            }
        }
        unset($entree);

        return $bailleursData;
    }

    /**
     * @return Locataire[]
     */
    protected function creerLocataires(): array
    {
        $noms = [
            ['Awa', 'Sow'], ['Ibrahima', 'Ba'], ['Fatou', 'Cissé'], ['Cheikh', 'Gueye'],
            ['Mariama', 'Diouf'], ['Alioune', 'Faye'], ['Khady', 'Sy'], ['Moussa', 'Kane'],
            ['Ndeye', 'Thiam'], ['Abdou', 'Seck'], ['Coumba', 'Ndiaye'], ['Lamine', 'Camara'],
            ['Sokhna', 'Diagne'], ['Pape', 'Sylla'], ['Marième', 'Sarr'], ['Assane', 'Wade'],
            ['Ramatoulaye', 'Fofana'], ['Serigne', 'Diakhaté'], ['Bineta', 'Coly'], ['Amadou', 'Traoré'],
            ['Aminata', 'Diatta'], ['Souleymane', 'Barry'],
        ];

        $locataires = [];
        foreach ($noms as $index => [$prenom, $nom]) {
            $locataires[] = Locataire::create([
                'numero' => NumeroService::genererNumero(Locataire::class, 'LOCT'),
                'prenom' => $prenom,
                'nom' => $nom,
                'telephone' => '76'.str_pad((string) (200000 + $index), 7, '0', STR_PAD_LEFT),
                'type_locataire' => 'particulier',
                'statut' => 'actif',
            ]);
        }

        return $locataires;
    }

    /**
     * Répartit les 26 biens entre les 22 locataires : les 18 premiers locataires reçoivent un
     * seul contrat, les 4 derniers en reçoivent deux (contrats de location différents, signés à
     * des dates différentes) — pour bien couvrir le cas "locataire avec plusieurs contrats".
     *
     * @return array<int, array{contrat: ContratLocation, bailleur: Bailleur, locataire: Locataire, bonPayeur: bool}>
     */
    protected function creerLocationsEtContrats(array $bailleursData, array $locataires): array
    {
        $biensAPlat = [];
        foreach ($bailleursData as $entree) {
            foreach ($entree['biens'] as $b) {
                $biensAPlat[] = ['bien' => $b['bien'], 'usage' => $b['usage'], 'bailleur' => $entree['bailleur']];
            }
        }

        // 22 locataires, 4 derniers avec 2 contrats chacun => 18 + 8 = 26 contrats = 26 biens.
        $assignations = [];
        for ($i = 0; $i < 18; $i++) {
            $assignations[] = [$i, [array_shift($biensAPlat)]];
        }
        for ($i = 18; $i < 22; $i++) {
            $assignations[] = [$i, [array_shift($biensAPlat), array_shift($biensAPlat)]];
        }

        // Locataires "en arriéré" (paiements incomplets) : environ 40% du panel, répartis parmi
        // locataires simples et multi-contrats pour varier les cas de figure.
        $indexArrieres = [2, 5, 7, 9, 12, 14, 16, 19, 21];

        $joursEcheance = [1, 5, 10, 15, 28];
        // 3 contrats résiliés plus tôt dans l'année, pour illustrer les cautions restituées.
        $indexContratsResilies = [3, 11, 20];

        $contrats = [];
        $indexContratGlobal = 0;

        foreach ($assignations as [$idxLocataire, $biensDuLocataire]) {
            $locataire = $locataires[$idxLocataire];
            $bonPayeur = ! in_array($idxLocataire, $indexArrieres, true);

            $location = Location::create([
                'numero' => NumeroService::genererNumero(Location::class, 'LOC'),
                'locataire_id' => $locataire->id,
            ]);

            foreach ($biensDuLocataire as $offset => $infosBien) {
                $bien = $infosBien['bien'];
                $bailleur = $infosBien['bailleur'];

                // Étale les débuts de location entre janvier et août 2026 : le 2e contrat d'un
                // locataire multi-biens démarre toujours plus tard que le premier.
                $moisDebut = $offset === 0
                    ? 1 + ($indexContratGlobal % 6)
                    : min(8, (1 + ($indexContratGlobal % 6)) + 3);
                $dateDebut = Carbon::create(2026, $moisDebut, 1);

                $estResilie = in_array($indexContratGlobal, $indexContratsResilies, true);
                $dateFin = $estResilie ? $dateDebut->copy()->addMonths(2)->endOfMonth() : null;

                $jourEcheance = $joursEcheance[$indexContratGlobal % count($joursEcheance)];

                $depotGarantie = 0.0;
                $partBailleurDepot = 0.0;
                $partAgenceDepot = 0.0;
                $aUneCaution = $indexContratGlobal % 5 !== 4; // 80% des contrats ont une caution
                if ($aUneCaution) {
                    $depotGarantie = (float) $bien->loyer_mensuel * 2;
                    $partBailleurDepot = (float) $bien->loyer_mensuel;
                    $partAgenceDepot = (float) $bien->loyer_mensuel;
                }

                $contrat = ContratLocation::create([
                    'numero' => NumeroService::genererNumeroBail($location->numero, $offset + 1),
                    'location_id' => $location->id,
                    'bien_id' => $bien->id,
                    'bailleur_id' => $bailleur->id,
                    'type_location' => $infosBien['usage'] === 'commercial' ? 'commercial' : 'habitation',
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'loyer_mensuel' => $bien->loyer_mensuel,
                    'depot_garantie' => $depotGarantie,
                    'depot_garantie_part_bailleur' => $aUneCaution ? $partBailleurDepot : null,
                    'depot_garantie_part_agence' => $aUneCaution ? $partAgenceDepot : null,
                    'jour_echeance' => $jourEcheance,
                    'appliquer_tva' => $indexContratGlobal % 3 !== 0,
                    'appliquer_tom' => $indexContratGlobal % 2 === 0,
                    'statut' => $estResilie ? 'resilie' : 'actif',
                    'notes' => 'Contrat créé par la simulation logique.',
                ]);

                $bien->update(['statut' => $estResilie ? 'disponible' : 'occupe']);

                // Échéances uniquement de janvier à août 2026 (et jamais après la résiliation).
                $dernierMois = $estResilie ? $dateFin->month : 8;
                if ($dernierMois >= $moisDebut) {
                    $this->echeances->genererLoyersManuel($contrat, 2026, range($moisDebut, $dernierMois));
                }

                if ($aUneCaution) {
                    $caution = Caution::create([
                        'contrat_location_id' => $contrat->id,
                        'montant_total' => $depotGarantie,
                        'part_bailleur' => $partBailleurDepot,
                        'part_agence' => $partAgenceDepot,
                        'statut' => 'detenue',
                    ]);

                    Paiement::create([
                        'numero' => NumeroService::genererNumero(Paiement::class, 'PAY'),
                        'contrat_location_id' => $contrat->id,
                        'type' => Paiement::TYPE_ENTREE,
                        'montant' => $depotGarantie,
                        'part_caution' => $partBailleurDepot,
                        'part_frais_agence' => $partAgenceDepot,
                        'mode_paiement_id' => $this->modePaiementAleatoire(),
                        'date_paiement' => $dateDebut,
                        'note' => "Encaissement à la signature — caution/garantie et frais d'agence",
                        'enregistre_par_id' => $this->acteurId,
                    ]);

                    // Sur les contrats résiliés : restitution simulée (une partielle, une totale).
                    if ($estResilie) {
                        $totale = $indexContratGlobal === $indexContratsResilies[0];
                        $montantRetenu = $totale ? 0 : round($partBailleurDepot * 0.3, 2);
                        $caution->update([
                            'statut' => $totale ? 'restituee' : 'partiellement_restituee',
                            'montant_retenu' => $montantRetenu,
                            'motif_retenue' => $totale ? null : 'Remise en état des lieux à la charge du locataire (peinture, nettoyage).',
                            'montant_restitue' => round($partBailleurDepot - $montantRetenu, 2),
                            'date_restitution' => $dateFin->copy()->addDays(10),
                            'restituee_par_id' => $this->acteurId,
                        ]);
                    }
                }

                try {
                    $type = $this->documents->typePourContratLocation($contrat);
                    $this->documents->generateFor($contrat, $type);
                } catch (\Throwable $e) {
                    // La génération de document ne doit jamais bloquer la simulation.
                }

                $contrats[] = [
                    'contrat' => $contrat,
                    'bailleur' => $bailleur,
                    'locataire' => $locataire,
                    'bonPayeur' => $bonPayeur,
                    'moisDebut' => $moisDebut,
                    'dernierMois' => $dernierMois,
                ];

                $indexContratGlobal++;
            }
        }

        return $contrats;
    }

    protected function modePaiementAleatoire(): int
    {
        return $this->modesPaiementIds[array_rand($this->modesPaiementIds)];
    }

    /**
     * Encaisse les échéances de chaque contrat : les locataires "bons payeurs" règlent tout,
     * les locataires "en arriéré" ne règlent qu'une partie de leurs échéances (certaines restent
     * intégralement impayées, d'autres partiellement payées), laissant un arriéré visible.
     */
    protected function simulerEncaissements(array $contrats): void
    {
        foreach ($contrats as $info) {
            $contrat = $info['contrat'];
            $echeances = EcheanceLoyer::where('contrat_location_id', $contrat->id)->orderBy('date_echeance')->get();

            foreach ($echeances as $position => $echeance) {
                $ventilation = $this->ventilation->ventilerLoyer($contrat, (float) $echeance->montant_attendu);

                if ($info['bonPayeur']) {
                    $this->encaisser($contrat, $echeance, (float) $echeance->montant_attendu, $ventilation);

                    continue;
                }

                // Locataire en arriéré : un tour sur deux payé intégralement, un tour sur quatre
                // payé partiellement, le reste laissé impayé (en_retard / à venir).
                $motif = $position % 4;
                if ($motif === 0 || $motif === 1) {
                    $this->encaisser($contrat, $echeance, (float) $echeance->montant_attendu, $ventilation);
                } elseif ($motif === 2) {
                    $montantPartiel = round((float) $echeance->montant_attendu * 0.55, 2);
                    $ventilationPartielle = $this->ventilation->ventilerLoyer($contrat, $montantPartiel);
                    $this->encaisser($contrat, $echeance, $montantPartiel, $ventilationPartielle);
                } else {
                    // motif === 3 : rien n'est encaissé — recalcule explicitement le statut pour
                    // que les échéances déjà passées apparaissent bien "en retard" (le statut
                    // initial "à venir" ne se met sinon à jour qu'au moment d'un encaissement).
                    $echeance->recalculerStatut();
                }
            }
        }
    }

    protected function encaisser(ContratLocation $contrat, EcheanceLoyer $echeance, float $montant, array $ventilation): void
    {
        $datePaiement = $echeance->date_echeance->copy()->addDays(random_int(0, 4));
        if ($datePaiement->greaterThan(now())) {
            $datePaiement = now()->copy()->subDays(random_int(0, 2));
        }

        Paiement::create([
            'numero' => NumeroService::genererNumero(Paiement::class, 'PAY'),
            'echeance_loyer_id' => $echeance->id,
            'contrat_location_id' => $contrat->id,
            'type' => Paiement::TYPE_LOYER,
            'montant' => $montant,
            'part_bailleur' => $ventilation['part_bailleur'],
            'part_commission_agence' => $ventilation['part_commission_agence'],
            'mode_paiement_id' => $this->modePaiementAleatoire(),
            'date_paiement' => $datePaiement,
            'note' => 'Encaissement loyer — simulation logique.',
            'enregistre_par_id' => $this->acteurId,
            'statut' => 'valide',
        ]);

        $echeance->recalculerMontantPaye();
    }

    /**
     * Crée quelques dépenses de location par bailleur (plomberie, peinture, urgence...), dans
     * des états variés : certaines déjà payées (impactent le compte du bailleur), d'autres
     * encore en attente de validation ou d'approbation.
     */
    protected function simulerDepenses(array $bailleursData, array $contrats): void
    {
        $categories = CategorieDepense::pluck('id', 'nom');
        $libellesParCategorie = [
            'Rénovation' => 'Rafraîchissement peinture et menuiseries',
            'Travaux importants' => 'Réfection étanchéité toiture',
            'Charges communes' => 'Quote-part charges communes de l\'immeuble',
            'Urgence' => 'Réparation fuite de plomberie',
            'Dégradation' => 'Remise en état après dégât des eaux',
            'Administrative' => 'Frais de dossier / état des lieux',
            'Agence' => 'Frais de gestion technique agence',
        ];
        $quiSupporteParCategorie = [
            'Rénovation' => 'bailleur',
            'Travaux importants' => 'bailleur',
            'Charges communes' => 'bailleur',
            'Urgence' => 'bailleur',
            'Dégradation' => 'locataire',
            'Administrative' => 'agence',
            'Agence' => 'agence',
        ];

        $statutsCycle = [DepenseLocation::STATUT_PAYEE, DepenseLocation::STATUT_PAYEE, DepenseLocation::STATUT_EN_ATTENTE, DepenseLocation::STATUT_APPROUVEE, DepenseLocation::STATUT_PAYEE];

        $contratsParBailleur = collect($contrats)->groupBy(fn ($c) => $c['bailleur']->id);

        $compteur = 0;
        foreach ($bailleursData as $entree) {
            $bailleur = $entree['bailleur'];
            $contratsBailleur = $contratsParBailleur->get($bailleur->id, collect());
            if ($contratsBailleur->isEmpty()) {
                continue;
            }

            $nbDepenses = $entree['nbBiens'] >= 3 ? 2 : 1;

            for ($k = 0; $k < $nbDepenses; $k++) {
                $categorieNom = array_keys($libellesParCategorie)[$compteur % count($libellesParCategorie)];
                $categorieId = $categories[$categorieNom] ?? $categories->first();
                $contratRef = $contratsBailleur[$compteur % $contratsBailleur->count()]['contrat'];
                // Le montant est indexé sur le loyer du bien concerné pour rester proportionné
                // aux revenus du bailleur (évite qu'une petite structure se retrouve avec une
                // dépense qui dépasse tout ce qu'elle encaisse).
                $fraction = [0.2, 0.3, 0.4, 0.5, 0.35, 0.6][$compteur % 6];
                $montant = round((float) $contratRef->loyer_mensuel * $fraction, -3);
                $statut = $statutsCycle[$compteur % count($statutsCycle)];
                $estPayee = in_array($statut, DepenseLocation::STATUTS_PAYEES, true);

                DepenseLocation::create([
                    'numero' => NumeroService::genererNumero(DepenseLocation::class, 'DEP'),
                    'bien_id' => $contratRef->bien_id,
                    'contrat_location_id' => $contratRef->id,
                    'bailleur_id' => $bailleur->id,
                    'categorie_depense_id' => $categorieId,
                    'description' => $libellesParCategorie[$categorieNom],
                    'montant_estime' => $montant,
                    'montant_final' => $estPayee ? $montant : null,
                    'fournisseur' => 'Prestataire local',
                    'urgence' => $categorieNom === 'Urgence',
                    'qui_supporte' => $quiSupporteParCategorie[$categorieNom],
                    'statut' => $statut,
                    'date_validation' => $statut !== DepenseLocation::STATUT_EN_ATTENTE ? Carbon::create(2026, min(8, 2 + ($compteur % 6)), 10) : null,
                    'date_paiement' => $estPayee ? Carbon::create(2026, min(8, 3 + ($compteur % 6)), 15) : null,
                    'mode_paiement_id' => $estPayee ? $this->modePaiementAleatoire() : null,
                    'cree_par_id' => $this->acteurId,
                ]);

                $compteur++;
            }
        }
    }

    /**
     * Verse à la majorité des bailleurs un montant inférieur à ce qui leur est réellement dû
     * (loyers encaissés - commission agence - dépenses payées), pour simuler un solde "à
     * reverser" réaliste (arriéré agence -> bailleur). Une minorité de bailleurs est réglée
     * intégralement.
     */
    protected function simulerVersements(array $bailleursData): void
    {
        // 3 bailleurs sur 10 soldés intégralement, les 7 autres partiellement.
        $indexSoldesIntegralement = [0, 4, 8];

        foreach ($bailleursData as $i => $entree) {
            $bailleur = $entree['bailleur'];

            $contratIds = ContratLocation::where('bailleur_id', $bailleur->id)->pluck('id');
            $echeanceIds = EcheanceLoyer::whereIn('contrat_location_id', $contratIds)->pluck('id');

            $paiementsLoyer = Paiement::where('statut', 'valide')
                ->where('type', 'loyer')
                ->where(fn ($q) => $q->whereIn('contrat_location_id', $contratIds)->orWhereIn('echeance_loyer_id', $echeanceIds))
                ->get();

            $loyersEncaisses = (float) $paiementsLoyer->sum('montant');
            $commissionAgence = (float) $paiementsLoyer->sum('part_commission_agence');
            $travauxDepenses = (float) DepenseLocation::where('bailleur_id', $bailleur->id)
                ->whereIn('statut', DepenseLocation::STATUTS_PAYEES)
                ->get()
                ->sum(fn ($d) => $d->montantImpute());

            $aReverserTheorique = round($loyersEncaisses - $commissionAgence - $travauxDepenses, 2);

            if ($aReverserTheorique <= 0) {
                continue;
            }

            $soldeIntegral = in_array($i, $indexSoldesIntegralement, true);
            $montantAVerser = $soldeIntegral
                ? $aReverserTheorique
                : round($aReverserTheorique * (0.45 + (($i % 3) * 0.1)), 2); // ~45% à 65% du dû

            if ($montantAVerser <= 0) {
                continue;
            }

            VersementBailleur::create([
                'numero' => NumeroService::genererNumero(VersementBailleur::class, 'VER'),
                'bailleur_id' => $bailleur->id,
                'montant' => $montantAVerser,
                'type' => 'normal',
                'date_versement' => Carbon::create(2026, 8, 18),
                'mode_paiement_id' => $this->modePaiementAleatoire(),
                'reference' => 'Versement simulation logique',
                'notes' => $soldeIntegral ? 'Solde entièrement versé au bailleur.' : 'Versement partiel — reliquat à reverser.',
                'effectue_par_id' => $this->acteurId,
            ]);
        }
    }
}
