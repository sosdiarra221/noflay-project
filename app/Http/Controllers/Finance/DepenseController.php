<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\Bien;
use App\Models\CategorieDepense;
use App\Models\ContratLocation;
use App\Models\DepenseLocation;
use App\Models\ModePaiement;
use App\Models\User;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('finance.consulter');

        $depenses = DepenseLocation::with(['bien', 'bailleur', 'locataire', 'categorie', 'contratLocation'])
            ->when($request->filled('bien_id'), fn ($q) => $q->where('bien_id', $request->bien_id))
            ->when($request->filled('bailleur_id'), fn ($q) => $q->where('bailleur_id', $request->bailleur_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('qui_supporte'), fn ($q) => $q->where('qui_supporte', $request->qui_supporte))
            ->latest()
            ->get();

        $stats = [
            'en_cours' => DepenseLocation::whereNotIn('statut', [DepenseLocation::STATUT_CLOTUREE, DepenseLocation::STATUT_REFUSEE])->count(),
            'a_payer' => (float) DepenseLocation::where('statut', DepenseLocation::STATUT_A_PAYER)->get()->sum->montantImpute(),
            'payees_mois' => (float) DepenseLocation::whereIn('statut', DepenseLocation::STATUTS_PAYEES)
                ->whereMonth('date_paiement', now()->month)->whereYear('date_paiement', now()->year)
                ->get()->sum->montantImpute(),
            'a_charge_bailleurs_mois' => (float) DepenseLocation::whereIn('statut', DepenseLocation::STATUTS_PAYEES)
                ->where('qui_supporte', 'bailleur')
                ->whereMonth('date_paiement', now()->month)->whereYear('date_paiement', now()->year)
                ->get()->sum->montantImpute(),
        ];

        $biens = Bien::orderBy('titre')->get();
        $bailleurs = Bailleur::orderBy('nom')->get();

        return view('finance.depenses.index', compact('depenses', 'stats', 'biens', 'bailleurs'));
    }

    public function create()
    {
        Gate::authorize('finance.gerer');

        $biens = Bien::with('bailleur')->orderBy('titre')->get();
        $categories = CategorieDepense::where('actif', true)->orderBy('nom')->get();
        $responsables = User::orderBy('name')->get();
        $contratsParBien = ContratLocation::where('statut', 'actif')
            ->with('location.locataire')
            ->get()
            ->groupBy('bien_id')
            ->map(fn ($contrats) => $contrats->map(fn ($c) => [
                'id' => $c->id,
                'numero' => $c->numero,
                'locataire' => $c->location?->locataire?->nom_complet,
            ])->values());

        return view('finance.depenses.create', compact('biens', 'categories', 'responsables', 'contratsParBien'));
    }

    public function store(Request $request)
    {
        Gate::authorize('finance.gerer');

        $data = $request->validate([
            'bien_id' => ['required', 'exists:biens,id'],
            'contrat_location_id' => ['nullable', 'exists:contrats_location,id'],
            'categorie_depense_id' => ['required', 'exists:categories_depense,id'],
            'description' => ['required', 'string'],
            'montant_estime' => ['required', 'numeric', 'min:0'],
            'fournisseur' => ['nullable', 'string', 'max:255'],
            'urgence' => ['nullable'],
            'qui_supporte' => ['required', 'in:bailleur,locataire,agence'],
            'responsable_financier_id' => ['nullable', 'exists:users,id'],
            'commentaire' => ['nullable', 'string'],
            'action' => ['required', 'in:brouillon,soumettre'],
        ]);

        $bien = Bien::findOrFail($data['bien_id']);
        $contrat = $data['contrat_location_id'] ? ContratLocation::find($data['contrat_location_id']) : null;
        $urgence = ! empty($data['urgence']);

        $statut = match (true) {
            $data['action'] === 'brouillon' => DepenseLocation::STATUT_BROUILLON,
            $urgence => DepenseLocation::STATUT_APPROUVEE,
            default => DepenseLocation::STATUT_EN_ATTENTE,
        };

        $depense = DepenseLocation::create([
            'numero' => NumeroService::genererNumero(DepenseLocation::class, 'DEP'),
            'bien_id' => $bien->id,
            'contrat_location_id' => $contrat?->id,
            'bailleur_id' => $bien->bailleur_id,
            'locataire_id' => $contrat?->location?->locataire_id,
            'categorie_depense_id' => $data['categorie_depense_id'],
            'description' => $data['description'],
            'montant_estime' => $data['montant_estime'],
            'fournisseur' => $data['fournisseur'] ?? null,
            'urgence' => $urgence,
            'qui_supporte' => $data['qui_supporte'],
            'responsable_financier_id' => $data['responsable_financier_id'] ?? null,
            'statut' => $statut,
            'commentaire' => $data['commentaire'] ?? null,
            'cree_par_id' => auth()->id(),
            'date_validation' => $statut === DepenseLocation::STATUT_APPROUVEE ? now() : null,
        ]);

        return redirect()->route('finance.depenses.show', $depense)->with('success', 'Dépense « '.$depense->numero.' » créée avec succès.');
    }

    public function show(DepenseLocation $depense)
    {
        Gate::authorize('finance.consulter');

        $depense->load(['bien', 'bailleur', 'locataire', 'categorie', 'contratLocation', 'responsableFinancier', 'creePar', 'modePaiement']);
        $modesPaiement = ModePaiement::where('actif', true)->orderBy('nom')->get();

        return view('finance.depenses.show', compact('depense', 'modesPaiement'));
    }

    public function soumettre(DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_BROUILLON]);

        $depense->update(['statut' => DepenseLocation::STATUT_EN_ATTENTE]);

        return back()->with('success', 'Dépense soumise pour validation.');
    }

    public function approuver(DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_EN_ATTENTE]);

        $depense->update(['statut' => DepenseLocation::STATUT_APPROUVEE, 'date_validation' => now()]);

        return back()->with('success', 'Dépense approuvée.');
    }

    public function refuser(Request $request, DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_EN_ATTENTE]);

        $data = $request->validate(['motif_refus' => ['required', 'string']]);

        $depense->update([
            'statut' => DepenseLocation::STATUT_REFUSEE,
            'motif_refus' => $data['motif_refus'],
            'date_validation' => now(),
        ]);

        return back()->with('success', 'Dépense refusée.');
    }

    public function demarrerIntervention(DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_APPROUVEE]);

        $depense->update(['statut' => DepenseLocation::STATUT_INTERVENTION]);

        return back()->with('success', 'Intervention marquée en cours.');
    }

    public function factureRecue(Request $request, DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_INTERVENTION, DepenseLocation::STATUT_APPROUVEE]);

        $data = $request->validate(['montant_final' => ['required', 'numeric', 'min:0']]);

        $depense->update(['statut' => DepenseLocation::STATUT_FACTURE_RECUE, 'montant_final' => $data['montant_final']]);

        return back()->with('success', 'Facture enregistrée. Montant final : '.number_format($data['montant_final'], 0, ',', ' ').' FCFA.');
    }

    public function marquerAPayer(DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_FACTURE_RECUE]);

        $depense->update(['statut' => DepenseLocation::STATUT_A_PAYER]);

        return back()->with('success', 'Dépense marquée à payer.');
    }

    /**
     * Enregistre le paiement effectif de la dépense : c'est ce statut (payée) qui déclenche
     * l'impact réel sur les comptes — déduction du reversement bailleur si qui_supporte =
     * bailleur, cf. ReversementService::calculerPourPeriode().
     */
    public function payer(Request $request, DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_A_PAYER]);

        $data = $request->validate([
            'mode_paiement_id' => ['required', 'exists:modes_paiement,id'],
            'date_paiement' => ['required', 'date'],
        ]);

        $depense->update([
            'statut' => DepenseLocation::STATUT_PAYEE,
            'mode_paiement_id' => $data['mode_paiement_id'],
            'date_paiement' => $data['date_paiement'],
        ]);

        return back()->with('success', 'Paiement enregistré. Dépense imputée au compte '.$depense->qui_supporte.'.');
    }

    public function cloturer(DepenseLocation $depense)
    {
        Gate::authorize('finance.gerer');
        $this->verifierStatut($depense, [DepenseLocation::STATUT_PAYEE, DepenseLocation::STATUT_REFUSEE]);

        $depense->update(['statut' => DepenseLocation::STATUT_CLOTUREE]);

        return back()->with('success', 'Dossier de dépense clôturé.');
    }

    protected function verifierStatut(DepenseLocation $depense, array $statutsAttendus): void
    {
        abort_unless(in_array($depense->statut, $statutsAttendus, true), 422, 'Transition de statut invalide.');
    }
}
