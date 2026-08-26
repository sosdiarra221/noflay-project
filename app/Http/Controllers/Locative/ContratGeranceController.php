<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\CategorieBien;
use App\Models\ContratGerance;
use App\Models\ContratLocation;
use App\Models\Documents\Document as DocumentGenere;
use App\Models\EcheanceLoyer;
use App\Models\FicheLocative;
use App\Models\Paiement;
use App\Models\Locative\ParametreLocative;
use App\Services\Locative\NumeroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContratGeranceController extends Controller
{
    public function index(Request $request)
    {
        $gerances = ContratGerance::with('bailleur')->withCount('biens')
            ->when($request->filled('bailleur_id'), fn ($q) => $q->where('bailleur_id', $request->bailleur_id))
            ->when($request->filled('type_gerance'), fn ($q) => $q->where('type_gerance', $request->type_gerance))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->latest()
            ->get();

        $bailleurs = Bailleur::orderBy('nom')->get();

        return view('locative.gerances.index', compact('gerances', 'bailleurs'));
    }

    public function show(ContratGerance $gerance)
    {
        $gerance->load(['bailleur', 'biens.categorie']);
        $categories = CategorieBien::where('actif', true)->orderBy('nom')->get();

        // Document généré par le nouveau moteur de modèles (module Gestion Document), s'il existe.
        $documentGenere = DocumentGenere::where('documentable_type', ContratGerance::class)
            ->where('documentable_id', $gerance->id)
            ->where('status', '!=', DocumentGenere::STATUT_CANCELLED)
            ->latest('generated_at')
            ->first();

        $kpis = Gate::allows('locative.finances') ? $this->calculerKpisFinanciers($gerance) : null;

        return view('locative.gerances.show', compact('gerance', 'categories', 'documentGenere', 'kpis'));
    }

    /**
     * Ce que touche l'agence via cette gérance (commission de gestion) et les taxes collectées
     * sur les biens rattachés, pour le mois en cours + une tendance sur 12 mois.
     */
    protected function calculerKpisFinanciers(ContratGerance $gerance): array
    {
        $bienIds = $gerance->biens->pluck('id');
        $contratIds = ContratLocation::whereIn('bien_id', $bienIds)->pluck('id');
        $echeanceIds = EcheanceLoyer::whereIn('contrat_location_id', $contratIds)->pluck('id');

        $requetePaiements = fn ($debut, $fin) => Paiement::where('statut', 'valide')
            ->where('type', 'loyer')
            ->where(fn ($q) => $q->whereIn('contrat_location_id', $contratIds)->orWhereIn('echeance_loyer_id', $echeanceIds))
            ->whereBetween('date_paiement', [$debut, $fin])
            ->get();

        $paiementsMois = $requetePaiements(now()->startOfMonth(), now()->endOfMonth());
        $fichesMois = FicheLocative::whereIn('contrat_location_id', $contratIds)
            ->where('annee', now()->year)->where('mois', now()->month)
            ->get();

        $tendance = collect(range(11, 0))->map(function ($moisAvant) use ($requetePaiements) {
            $date = now()->subMonths($moisAvant);
            $paiements = $requetePaiements($date->copy()->startOfMonth(), $date->copy()->endOfMonth());

            return [
                'libelle' => ucfirst($date->translatedFormat('M Y')),
                'commission' => (float) $paiements->sum('part_commission_agence'),
                'loyers' => (float) $paiements->sum('montant'),
            ];
        });

        return [
            'commission_mois' => (float) $paiementsMois->sum('part_commission_agence'),
            'loyers_encaisses_mois' => (float) $paiementsMois->sum('montant'),
            'tva_mois' => (float) $fichesMois->sum('montant_tva'),
            'tom_mois' => (float) $fichesMois->sum('montant_tom'),
            'tendance' => $tendance,
        ];
    }

    public function create()
    {
        $bailleurs = Bailleur::where('statut', 'actif')->orderBy('nom')->get();

        return view('locative.gerances.create', compact('bailleurs'));
    }

    public function store(Request $request, \App\Services\Documents\DocumentGenerationService $documentGenerationService)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(ContratGerance::class, 'GER');

        $gerance = ContratGerance::create($data);

        // Génération automatique du mandat de gérance (module Gestion Document) — ne doit jamais
        // bloquer la création du contrat de gérance (même règle que pour les locations).
        $notice = null;
        try {
            $document = $documentGenerationService->generateFor($gerance, \App\Services\Documents\DocumentType::MANDAT_GERANCE);
            if (! $document) {
                $notice = "Aucun modèle actif n'est configuré pour générer le mandat de gérance. Vous pouvez configurer un modèle dans Gestion Document.";
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Échec de génération automatique du mandat de gérance pour '.$gerance->numero.' : '.$e->getMessage());
            $notice = "Aucun modèle actif n'est configuré pour générer le mandat de gérance. Vous pouvez configurer un modèle dans Gestion Document.";
        }

        $redirection = redirect()->route('locative.gerances.show', $gerance)->with('success', 'Contrat de gérance créé avec succès.');

        return $notice ? $redirection->with('notice', $notice) : $redirection;
    }

    public function update(Request $request, ContratGerance $gerance)
    {
        $data = $this->valider($request);

        $champsFinanciers = ['frais_gestion_mode', 'frais_gestion_valeur', 'tva_charge', 'taxe_charge', 'tom_charge'];
        $modifieFinancier = collect($champsFinanciers)->contains(fn ($champ) => (string) $gerance->{$champ} !== (string) $data[$champ]);

        if ($gerance->statut === 'actif' && $modifieFinancier) {
            Gate::authorize('locative.operations-sensibles');

            $request->validate([
                'motif' => ['required', 'string', 'max:255'],
            ]);

            $gerance->motifAction = $request->motif;
        }

        $gerance->update($data);

        return back()->with('success', 'Contrat de gérance mis à jour avec succès.');
    }

    public function destroy(Request $request, ContratGerance $gerance)
    {
        Gate::authorize('locative.operations-sensibles');

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $gerance->motif_suppression = $request->motif_suppression;
        $gerance->supprime_par_id = auth()->id();
        $gerance->save();
        $gerance->delete();

        return redirect()->route('locative.gerances.index')->with('success', 'Contrat de gérance supprimé avec succès.');
    }

    public function pdf(ContratGerance $gerance)
    {
        $gerance->load(['bailleur', 'biens.categorie']);

        $reglage = ParametreLocative::courant();
        $ville = $reglage->adresse ? trim(explode(',', $reglage->adresse)[0]) : 'Dakar';

        $pdf = Pdf::loadView('locative.pdf.mandat-gerance', compact('gerance', 'reglage', 'ville'));

        return $pdf->download('Mandat-gerance-'.$gerance->numero.'.pdf');
    }

    public function apercu(ContratGerance $gerance)
    {
        $gerance->load(['bailleur', 'biens.categorie']);

        $reglage = ParametreLocative::courant();
        $ville = $reglage->adresse ? trim(explode(',', $reglage->adresse)[0]) : 'Dakar';

        $pdf = Pdf::loadView('locative.pdf.mandat-gerance', compact('gerance', 'reglage', 'ville'));

        return $pdf->stream('Mandat-gerance-'.$gerance->numero.'.pdf');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'bailleur_id' => ['required', 'exists:bailleurs,id'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'type_gerance' => ['required', 'string', 'in:gestion_locative,gestion_vente,gestion_locative_vente'],
            'frais_gestion_mode' => ['required', 'string', 'in:pourcentage,montant_fixe'],
            'frais_gestion_valeur' => ['required', 'numeric', 'min:0'],
            'tva_charge' => ['required', 'string', 'in:bailleur,agence'],
            'taxe_charge' => ['required', 'string', 'in:bailleur,agence'],
            'tom_charge' => ['required', 'string', 'in:bailleur,agence'],
            'statut' => ['required', 'string', 'in:brouillon,en_attente_signature,actif,suspendu,expire,resilie,archive'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
