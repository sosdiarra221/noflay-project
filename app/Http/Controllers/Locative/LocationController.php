<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\ContratLocation;
use App\Models\Locataire;
use App\Models\Location;
use App\Models\ModePaiement;
use App\Services\Locative\EcheanceLoyerService;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with(['locataire', 'contrats.bien'])->latest()->get();

        return view('locative.locations.index', compact('locations'));
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
        $location->load(['locataire', 'contrats.bien', 'contrats.echeances']);

        return view('locative.locations.show', compact('location'));
    }

    public function store(Request $request, EcheanceLoyerService $echeanceService)
    {
        $data = $request->validate([
            'locataire_id' => ['required', 'exists:locataires,id'],
            'biens' => ['required', 'array', 'min:1'],
            'biens.*' => ['exists:biens,id'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'jour_echeance' => ['required', 'integer', 'min:1', 'max:28'],
            'mode_paiement_prefere_id' => ['nullable', 'exists:modes_paiement,id'],
            'conditions' => ['required', 'array'],
            'conditions.*.loyer_mensuel' => ['required', 'numeric', 'min:0'],
            'conditions.*.depot_garantie' => ['nullable', 'numeric', 'min:0'],
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

                $contrat = ContratLocation::create([
                    'numero' => NumeroService::genererNumeroBail($location->numero, $index),
                    'location_id' => $location->id,
                    'bien_id' => $bien->id,
                    'bailleur_id' => $bien->bailleur_id,
                    'date_debut' => $data['date_debut'],
                    'date_fin' => $data['date_fin'] ?? null,
                    'loyer_mensuel' => $conditions['loyer_mensuel'],
                    'depot_garantie' => $conditions['depot_garantie'] ?? 0,
                    'jour_echeance' => $data['jour_echeance'],
                    'charges' => $conditions['charges'] ?? 0,
                    'mode_paiement_prefere_id' => $data['mode_paiement_prefere_id'] ?? null,
                    'statut' => 'actif',
                ]);

                $bien->update(['statut' => 'occupe']);

                $echeanceService->genererPourContrat($contrat);

                $index++;
            }

            return $location;
        });

        return redirect()->route('locative.locations.show', $location)->with('success', 'Location créée avec succès.');
    }
}
