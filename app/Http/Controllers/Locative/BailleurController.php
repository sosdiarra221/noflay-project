<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\ContratLocation;
use App\Services\Finance\CompteBailleurService;
use App\Services\Finance\VentilationService;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BailleurController extends Controller
{
    public function index(Request $request)
    {
        $bailleurs = Bailleur::withCount(['gerances', 'biens'])
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%"));
            })
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->orderBy('nom')
            ->get();

        return view('locative.bailleurs.index', compact('bailleurs'));
    }

    public function show(Bailleur $bailleur, CompteBailleurService $compteBailleurService, VentilationService $ventilationService)
    {
        $bailleur->load(['gerances' => fn ($q) => $q->latest(), 'biens' => fn ($q) => $q->latest()]);

        $compte = null;
        $reversements = collect();
        $versements = collect();
        $rapport = null;
        if (Gate::allows('locative.finances')) {
            ['resume' => $compte, 'reversements' => $reversements, 'versements' => $versements] = $compteBailleurService->calculer($bailleur);
            $rapport = $this->construireRapportNarratif($bailleur, $compte, $ventilationService);
        }

        return view('locative.bailleurs.show', compact('bailleur', 'compte', 'reversements', 'versements', 'rapport'));
    }

    /**
     * Prépare les chiffres du "portrait mensuel" théorique du bailleur (loyer total actif,
     * commission de gestion moyenne, net mensuel à reverser) pour alimenter le rapport en texte
     * de la fiche bailleur, en s'appuyant sur les contrats de location actuellement actifs.
     */
    protected function construireRapportNarratif(Bailleur $bailleur, array $compte, VentilationService $ventilationService): array
    {
        $contratsActifs = ContratLocation::where('bailleur_id', $bailleur->id)
            ->where('statut', 'actif')
            ->with('bien.gerance')
            ->get();

        $loyerMensuelTotal = (float) $contratsActifs->sum('loyer_mensuel');
        $commissionMensuelle = (float) $contratsActifs->sum(
            fn (ContratLocation $contrat) => $ventilationService->ventilerLoyer($contrat, (float) $contrat->loyer_mensuel)['part_commission_agence']
        );
        $netMensuelTheorique = round($loyerMensuelTotal - $commissionMensuelle, 2);
        $tauxMoyen = $loyerMensuelTotal > 0 ? round($commissionMensuelle / $loyerMensuelTotal * 100, 1) : 0;

        $premiereGerance = $bailleur->gerances->sortBy('date_debut')->first();

        $biensOccupes = $bailleur->biens->where('statut', 'occupe')->count();
        $biensDisponibles = $bailleur->biens->whereIn('statut', ['disponible', 'reserve', 'en_attente_entree'])->count();

        return [
            'contrats_actifs' => $contratsActifs->count(),
            'loyer_mensuel_total' => $loyerMensuelTotal,
            'commission_mensuelle' => $commissionMensuelle,
            'net_mensuel_theorique' => $netMensuelTheorique,
            'taux_moyen' => $tauxMoyen,
            'premiere_gerance' => $premiereGerance,
            'biens_occupes' => $biensOccupes,
            'biens_disponibles' => $biensDisponibles,
        ];
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(Bailleur::class, 'BLR');

        $bailleur = Bailleur::create($data);

        return back()->with('success', 'Bailleur créé avec succès.')->with('bailleur_cree_id', $bailleur->id);
    }

    public function update(Request $request, Bailleur $bailleur)
    {
        $data = $this->valider($request);

        $bailleur->update($data);

        return back()->with('success', 'Bailleur mis à jour avec succès.');
    }

    public function destroy(Request $request, Bailleur $bailleur)
    {
        Gate::authorize('locative.operations-sensibles');

        if (ContratLocation::where('bailleur_id', $bailleur->id)->where('statut', 'actif')->exists()) {
            return back()->withErrors([
                'bailleur' => 'Ce bailleur a au moins un contrat de location actif : il ne peut pas être supprimé tant que ce(s) contrat(s) ne sont pas résiliés ou expirés.',
            ]);
        }

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $bailleur->motif_suppression = $request->motif_suppression;
        $bailleur->supprime_par_id = auth()->id();
        $bailleur->save();
        $bailleur->delete();

        return redirect()->route('locative.bailleurs.index')->with('success', 'Bailleur supprimé avec succès.');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'in:particulier,entreprise'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'piece_identite_type' => ['nullable', 'string', 'max:100'],
            'piece_identite_numero' => ['nullable', 'string', 'max:100'],
            'ninea' => ['nullable', 'string', 'max:100'],
            'rc' => ['nullable', 'string', 'max:100'],
            'coordonnees_paiement' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'statut' => ['required', 'string', 'in:actif,inactif'],
        ]);
    }
}
