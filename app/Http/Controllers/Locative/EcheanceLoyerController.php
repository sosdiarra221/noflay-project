<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\ContratLocation;
use App\Models\EcheanceLoyer;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Events\PaiementRecu;
use App\Models\Reglage;
use App\Services\Finance\VentilationService;
use App\Services\Locative\EcheanceLoyerService;
use App\Services\Locative\NumeroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcheanceLoyerController extends Controller
{
    public function index(Request $request)
    {
        $echeances = EcheanceLoyer::with(['contratLocation.bien', 'contratLocation.location.locataire', 'paiements.modePaiement'])
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('mois_annee'), function ($q) use ($request) {
                [$annee, $mois] = explode('-', $request->mois_annee);
                $q->where('annee', $annee)->where('mois', $mois);
            })
            ->when($request->filled('locataire_id'), function ($q) use ($request) {
                $q->whereHas('contratLocation.location', fn ($q2) => $q2->where('locataire_id', $request->locataire_id));
            })
            ->orderByDesc('date_echeance')
            ->get();

        $locataires = Locataire::orderBy('nom')->get();

        $contrats = ContratLocation::with(['bien', 'location.locataire'])
            ->where('statut', 'actif')
            ->get();

        $periodesDisponibles = EcheanceLoyer::selectRaw('annee, mois')
            ->distinct()
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->get()
            ->map(fn ($ligne) => [
                'valeur' => sprintf('%d-%02d', $ligne->annee, $ligne->mois),
                'libelle' => ucfirst(\Carbon\Carbon::createFromDate($ligne->annee, $ligne->mois, 1)->translatedFormat('F Y')),
            ]);

        return view('locative.echeances.index', compact('echeances', 'locataires', 'contrats', 'periodesDisponibles'));
    }

    public function encaisser(Request $request, EcheanceLoyer $echeance, VentilationService $ventilationService)
    {
        $data = $request->validate([
            'montant' => ['required', 'numeric', 'min:0.01'],
            'mode_paiement_id' => ['required', 'exists:modes_paiement,id'],
            'date_paiement' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $echeance, $ventilationService) {
            $contrat = $echeance->contratLocation;
            $ventilation = $ventilationService->ventilerLoyer($contrat, (float) $data['montant']);

            $paiement = Paiement::create([
                'numero' => NumeroService::genererNumero(Paiement::class, 'PAY'),
                'echeance_loyer_id' => $echeance->id,
                'contrat_location_id' => $contrat->id,
                'type' => Paiement::TYPE_LOYER,
                'montant' => $data['montant'],
                'part_bailleur' => $ventilation['part_bailleur'],
                'part_commission_agence' => $ventilation['part_commission_agence'],
                'mode_paiement_id' => $data['mode_paiement_id'],
                'date_paiement' => $data['date_paiement'],
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
                'enregistre_par_id' => auth()->id(),
            ]);

            $echeance->recalculerMontantPaye();

            event(new PaiementRecu($paiement));
        });

        return back()->with('success', 'Paiement enregistré avec succès.');
    }

    public function generer(Request $request, EcheanceLoyerService $echeanceService)
    {
        $data = $request->validate([
            'contrat_location_id' => ['required', 'exists:contrats_location,id'],
            'annee' => ['required', 'integer', 'min:2020', 'max:2100'],
            'mois' => ['required', 'array', 'min:1'],
            'mois.*' => ['integer', 'min:1', 'max:12'],
        ]);

        $contrat = ContratLocation::findOrFail($data['contrat_location_id']);

        $creees = $echeanceService->genererLoyersManuel($contrat, $data['annee'], $data['mois']);

        return back()->with('success', count($creees).' échéance(s) générée(s) avec succès.');
    }

    public function apercuQuittance(EcheanceLoyer $echeance)
    {
        [$reglage, $ville] = $this->donneesQuittance();
        $echeance->load(['contratLocation.bien', 'contratLocation.location.locataire']);
        $numero = $echeance->numeroQuittance();

        $pdf = Pdf::loadView('locative.pdf.quittance-loyer', compact('echeance', 'numero', 'reglage', 'ville'));

        return $pdf->stream($numero.'.pdf');
    }

    public function telechargerQuittance(EcheanceLoyer $echeance)
    {
        [$reglage, $ville] = $this->donneesQuittance();
        $echeance->load(['contratLocation.bien', 'contratLocation.location.locataire']);
        $numero = $echeance->numeroQuittance();

        $pdf = Pdf::loadView('locative.pdf.quittance-loyer', compact('echeance', 'numero', 'reglage', 'ville'));

        return $pdf->download($numero.'.pdf');
    }

    protected function donneesQuittance(): array
    {
        $reglage = Reglage::courant();
        $ville = $reglage->adresse ? trim(explode(',', $reglage->adresse)[0]) : 'Dakar';

        return [$reglage, $ville];
    }
}
