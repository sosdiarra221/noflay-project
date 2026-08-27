<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Caution;
use App\Models\DepenseLocation;
use App\Models\Facturation\Facture;
use App\Models\Paiement;
use App\Models\ReversementBailleur;
use App\Models\VersementBailleur;
use App\Services\Finance\ComptabiliteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class ComptabiliteController extends Controller
{
    public function index(Request $request, ComptabiliteService $service)
    {
        Gate::authorize('finance.comptabilite');

        $debut = $request->filled('debut') ? Carbon::parse($request->debut) : now()->startOfMonth();
        $fin = $request->filled('fin') ? Carbon::parse($request->fin) : now()->endOfMonth();

        $mouvements = $service->mouvements($debut, $fin)
            ->when($request->filled('type'), fn ($c) => $c->where('type', $request->type))
            ->when($request->filled('module'), fn ($c) => $c->where('module', $request->module));

        $stats = [
            'total_encaisse' => $service->totalEncaisse($debut, $fin),
            'total_decaisse' => $service->totalDecaisse($debut, $fin),
            'solde_periode' => round($service->totalEncaisse($debut, $fin) - $service->totalDecaisse($debut, $fin), 2),
            'solde_global' => $service->soldeGlobal(),
        ];

        $modules = $service->mouvements()->pluck('module')->unique()->sort()->values();

        return view('finance.comptabilite.index', [
            'mouvements' => $mouvements->values(),
            'stats' => $stats,
            'modules' => $modules,
            'debut' => $debut,
            'fin' => $fin,
        ]);
    }

    public function detail(string $type, int $id)
    {
        Gate::authorize('finance.comptabilite');

        $enregistrement = match ($type) {
            'paiement' => Paiement::with('contratLocation.bien', 'contratLocation.bailleur', 'modePaiement')->findOrFail($id),
            'facture' => Facture::with('client', 'lignes')->findOrFail($id),
            'depense' => DepenseLocation::with('bien', 'bailleur', 'categorie', 'modePaiement')->findOrFail($id),
            'caution' => Caution::with('contratLocation.bien', 'contratLocation.location.locataire')->findOrFail($id),
            'versement_bailleur' => VersementBailleur::with('bailleur', 'modePaiement')->findOrFail($id),
            'reversement_bailleur' => ReversementBailleur::with('bailleur', 'modePaiement')->findOrFail($id),
            default => abort(404),
        };

        return view('finance.comptabilite._detail', compact('type', 'enregistrement'));
    }

    public function rapportPdf(Request $request, ComptabiliteService $service)
    {
        Gate::authorize('finance.comptabilite');

        $debut = $request->filled('debut') ? Carbon::parse($request->debut) : now()->startOfMonth();
        $fin = $request->filled('fin') ? Carbon::parse($request->fin) : now()->endOfMonth();

        $mouvements = $service->mouvements($debut, $fin);
        $stats = [
            'total_encaisse' => $service->totalEncaisse($debut, $fin),
            'total_decaisse' => $service->totalDecaisse($debut, $fin),
            'solde_periode' => round($service->totalEncaisse($debut, $fin) - $service->totalDecaisse($debut, $fin), 2),
        ];

        $pdf = Pdf::loadView('finance.comptabilite.rapport-pdf', compact('mouvements', 'stats', 'debut', 'fin'));

        return $pdf->stream('rapport-comptabilite-'.$debut->format('Y-m-d').'-au-'.$fin->format('Y-m-d').'.pdf');
    }
}
