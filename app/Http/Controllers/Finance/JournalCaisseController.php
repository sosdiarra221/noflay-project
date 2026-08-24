<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Caution;
use App\Models\Paiement;
use App\Models\ReversementBailleur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JournalCaisseController extends Controller
{
    /**
     * Journal de caisse : la vue unifiée de tout le flux financier d'une journée donnée —
     * encaissements (loyers et paiements d'entrée, chacun déjà ventilé par destination) et
     * décaissements (reversements aux bailleurs, restitutions de caution).
     */
    public function index(Request $request)
    {
        Gate::authorize('finance.consulter');

        $date = $request->filled('date') ? Carbon::parse($request->date) : now();

        $encaissements = Paiement::where('statut', 'valide')
            ->whereDate('date_paiement', $date)
            ->with([
                'contratLocation.bien',
                'contratLocation.location.locataire',
                'echeance.contratLocation.bien',
                'echeance.contratLocation.location.locataire',
                'modePaiement',
            ])
            ->orderBy('created_at')
            ->get()
            ->map(function (Paiement $paiement) {
                $contrat = $paiement->contratLocation ?? $paiement->echeance?->contratLocation;

                return [
                    'heure' => $paiement->created_at?->format('H:i') ?? '—',
                    'numero' => $paiement->numero,
                    'type' => $paiement->type,
                    'bien' => $contrat?->bien?->titre ?? '—',
                    'locataire' => $contrat?->location?->locataire?->nom_complet ?? '—',
                    'mode_paiement' => $paiement->modePaiement->nom ?? '—',
                    'montant' => (float) $paiement->montant,
                    'part_bailleur' => (float) ($paiement->part_bailleur ?? 0),
                    'part_commission_agence' => (float) ($paiement->part_commission_agence ?? 0),
                    'part_caution' => (float) ($paiement->part_caution ?? 0),
                    'part_frais_agence' => (float) ($paiement->part_frais_agence ?? 0),
                ];
            });

        $reversements = ReversementBailleur::where('statut', 'verse')
            ->whereDate('date_versement', $date)
            ->with('bailleur', 'modePaiement')
            ->orderBy('updated_at')
            ->get();

        $restitutionsCaution = Caution::whereDate('date_restitution', $date)
            ->with('contratLocation.bien', 'contratLocation.location.locataire')
            ->orderBy('updated_at')
            ->get();

        $totaux = [
            'total_encaisse' => (float) $encaissements->sum('montant'),
            'vers_bailleurs' => (float) $encaissements->sum('part_bailleur'),
            'vers_agence' => (float) $encaissements->sum('part_commission_agence') + (float) $encaissements->sum('part_frais_agence'),
            'vers_caution' => (float) $encaissements->sum('part_caution'),
            'total_reverse' => (float) $reversements->sum('montant_net'),
            'total_restitue' => (float) $restitutionsCaution->sum('montant_restitue'),
        ];
        $totaux['solde_net'] = round($totaux['total_encaisse'] - $totaux['total_reverse'] - $totaux['total_restitue'], 2);

        return view('finance.journal-caisse.index', compact('date', 'encaissements', 'reversements', 'restitutionsCaution', 'totaux'));
    }
}
