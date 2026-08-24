<?php

namespace App\Http\Controllers\Locative;

use App\Events\LocationCreee;
use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Caution;
use App\Models\ContratLocation;
use App\Models\DepenseLocation;
use App\Models\Locataire;
use App\Models\Location;
use App\Models\ModePaiement;
use App\Models\Paiement;
use App\Services\Documents\DocumentGenerationService;
use App\Services\Locative\EcheanceLoyerService;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::with(['locataire', 'contrats.bien'])
            ->when($request->filled('locataire_id'), fn ($q) => $q->where('locataire_id', $request->locataire_id))
            ->when($request->filled('statut'), function ($q) use ($request) {
                $q->whereHas('contrats', fn ($q2) => $q2->where('statut', $request->statut));
            })
            ->latest()
            ->get();

        $locataires = Locataire::orderBy('nom')->get();

        return view('locative.locations.index', compact('locations', 'locataires'));
    }

    public function create()
    {
        $locataires = Locataire::where('statut', 'actif')->orderBy('nom')->get();
        $biensDisponibles = Bien::with(['bailleur', 'categorie'])
            ->where('type_exploitation', 'location')
            ->where('statut', 'disponible')
            ->orderBy('titre')
            ->get();
        $modesPaiement = ModePaiement::where('actif', true)->orderBy('nom')->get();

        return view('locative.locations.create', compact('locataires', 'biensDisponibles', 'modesPaiement'));
    }

    public function show(Location $location)
    {
        $location->load(['locataire', 'contrats.bien.categorie', 'contrats.echeances.paiements.modePaiement', 'contrats.caution']);

        $echeances = $location->contrats->flatMap->echeances->sortByDesc('date_echeance')->values();
        $paiements = $echeances->flatMap->paiements->sortByDesc('date_paiement')->values();

        $depenses = DepenseLocation::whereIn('contrat_location_id', $location->contrats->pluck('id'))
            ->with('bien', 'categorie')
            ->latest()
            ->get();

        $stats = [
            'loyer_total' => $location->contrats->sum('loyer_mensuel'),
            'total_attendu' => $echeances->where('statut', '!=', 'annule')->sum('montant_attendu'),
            'total_paye' => $echeances->sum('montant_paye'),
            'echeances_en_retard' => $echeances->where('statut', 'en_retard')->count(),
            'total_depenses' => $depenses->whereIn('statut', DepenseLocation::STATUTS_PAYEES)->sum(fn ($d) => $d->montantImpute()),
        ];

        return view('locative.locations.show', compact('location', 'stats', 'echeances', 'paiements', 'depenses'));
    }

    public function store(Request $request, EcheanceLoyerService $echeanceService, DocumentGenerationService $documentGenerationService)
    {
        $data = $request->validate([
            'type_location' => ['required', 'in:habitation,commercial'],
            'locataire_id' => ['required', 'exists:locataires,id'],
            'biens' => ['required', 'array', 'min:1'],
            'biens.*' => ['exists:biens,id'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'jour_echeance' => ['required', 'integer', 'min:1', 'max:28'],
            'mode_paiement_prefere_id' => ['nullable', 'exists:modes_paiement,id'],
            'conditions' => ['required', 'array'],
            'conditions.*.loyer_mensuel' => ['required', 'numeric', 'min:0'],
            'conditions.*.appliquer_tva' => ['nullable'],
            'conditions.*.appliquer_tom' => ['nullable'],
            'conditions.*.depot_garantie' => ['nullable', 'numeric', 'min:0'],
            'conditions.*.depot_garantie_part_bailleur' => ['nullable', 'numeric', 'min:0'],
            'conditions.*.depot_garantie_part_agence' => ['nullable', 'numeric', 'min:0'],
            'conditions.*.charges' => ['nullable', 'numeric', 'min:0'],
        ]);

        $biensSelectionnes = Bien::whereIn('id', $data['biens'])->get();

        $biensDejaOccupes = $biensSelectionnes->where('statut', '!=', 'disponible');
        if ($biensDejaOccupes->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'biens' => 'Le(s) bien(s) suivant(s) ne sont plus disponibles : '.$biensDejaOccupes->pluck('titre')->implode(', '),
            ]);
        }

        $location = DB::transaction(function () use ($data, $biensSelectionnes, $echeanceService) {
            $location = Location::create([
                'numero' => NumeroService::genererNumero(Location::class, 'LOC'),
                'locataire_id' => $data['locataire_id'],
            ]);

            $index = 1;
            foreach ($biensSelectionnes as $bien) {
                $conditions = $data['conditions'][$bien->id] ?? [];
                $depotGarantie = (float) ($conditions['depot_garantie'] ?? 0);
                $partBailleur = (float) ($conditions['depot_garantie_part_bailleur'] ?? 0);
                $partAgence = (float) ($conditions['depot_garantie_part_agence'] ?? 0);

                $contrat = ContratLocation::create([
                    'numero' => NumeroService::genererNumeroBail($location->numero, $index),
                    'location_id' => $location->id,
                    'bien_id' => $bien->id,
                    'bailleur_id' => $bien->bailleur_id,
                    'type_location' => $data['type_location'],
                    'date_debut' => $data['date_debut'],
                    'date_fin' => $data['date_fin'] ?? null,
                    'loyer_mensuel' => $conditions['loyer_mensuel'],
                    'depot_garantie' => $depotGarantie,
                    'depot_garantie_part_bailleur' => $depotGarantie > 0 ? $partBailleur : null,
                    'depot_garantie_part_agence' => $depotGarantie > 0 ? $partAgence : null,
                    'jour_echeance' => $data['jour_echeance'],
                    'charges' => $conditions['charges'] ?? 0,
                    'appliquer_tva' => ! empty($conditions['appliquer_tva']),
                    'appliquer_tom' => ! empty($conditions['appliquer_tom']),
                    'mode_paiement_prefere_id' => $data['mode_paiement_prefere_id'] ?? null,
                    'statut' => 'actif',
                ]);

                $bien->update(['statut' => 'occupe']);

                $echeanceService->genererPourContrat($contrat);

                // Ventilation automatique de l'encaissement à la signature (règle fondamentale du
                // flux financier : jamais un simple "+montant en caisse", toujours une répartition
                // explicite caution/frais d'agence). La caution reste la propriété du bailleur et
                // est suivie dans un compte séparé, distinct du chiffre d'affaires de l'agence.
                if ($depotGarantie > 0) {
                    Caution::create([
                        'contrat_location_id' => $contrat->id,
                        'montant_total' => $depotGarantie,
                        'part_bailleur' => $partBailleur,
                        'part_agence' => $partAgence,
                        'statut' => 'detenue',
                    ]);

                    Paiement::create([
                        'numero' => NumeroService::genererNumero(Paiement::class, 'PAY'),
                        'contrat_location_id' => $contrat->id,
                        'type' => Paiement::TYPE_ENTREE,
                        'montant' => $depotGarantie,
                        'part_caution' => $partBailleur,
                        'part_frais_agence' => $partAgence,
                        'mode_paiement_id' => $data['mode_paiement_prefere_id'] ?? null,
                        'date_paiement' => $data['date_debut'],
                        'note' => "Encaissement à la signature — caution/garantie et frais d'agence",
                        'enregistre_par_id' => auth()->id(),
                    ]);
                }

                $index++;
            }

            return $location;
        });

        event(new LocationCreee($location));

        // Génération automatique du (des) document(s) contractuel(s) — module Gestion Document.
        //
        // Cette étape se fait volontairement APRÈS la validation de la transaction ci-dessus, et
        // chaque contrat est traité indépendamment : un échec de génération (aucun modèle actif
        // configuré pour ce type de contrat, etc.) ne doit jamais annuler ni bloquer la création
        // de la location déjà actée (cf. spécification, règle "la génération ne bloque jamais").
        $contratsSansModele = [];

        foreach ($location->contrats()->with('bien.categorie')->get() as $contrat) {
            try {
                $type = $documentGenerationService->typePourContratLocation($contrat);
                $document = $documentGenerationService->generateFor($contrat, $type);

                if (! $document) {
                    $contratsSansModele[] = $contrat->numero;
                }
            } catch (\Throwable $e) {
                Log::warning('Échec de génération automatique du document contractuel pour '.$contrat->numero.' : '.$e->getMessage());
                $contratsSansModele[] = $contrat->numero;
            }
        }

        $redirection = redirect()->route('locative.locations.show', $location)->with('success', 'Location créée avec succès.');

        if (! empty($contratsSansModele)) {
            $redirection->with('notice', "Aucun modèle actif n'est configuré pour générer le document contractuel de : ".implode(', ', $contratsSansModele).'. Vous pouvez configurer un modèle dans Gestion Document.');
        }

        return $redirection;
    }
}
