<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\ModePaiement;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RevenuController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('finance.consulter');

        $paiements = Paiement::with(['echeance.contratLocation.bien.gerance', 'echeance.contratLocation.location.locataire', 'modePaiement'])
            ->where('statut', 'valide')
            ->when($request->filled('bailleur_id'), function ($q) use ($request) {
                $q->whereHas('echeance.contratLocation', fn ($q2) => $q2->where('bailleur_id', $request->bailleur_id));
            })
            ->when($request->filled('mode_paiement_id'), fn ($q) => $q->where('mode_paiement_id', $request->mode_paiement_id))
            ->when($request->filled('mois_annee'), function ($q) use ($request) {
                [$annee, $mois] = explode('-', $request->mois_annee);
                $q->whereYear('date_paiement', $annee)->whereMonth('date_paiement', $mois);
            })
            ->orderByDesc('date_paiement')
            ->get();

        $lignes = $paiements->map(function (Paiement $paiement) {
            $bien = $paiement->echeance->contratLocation->bien ?? null;
            $mode = $bien->frais_gestion_mode ?? $bien->gerance?->frais_gestion_mode ?? 'pourcentage';
            $valeur = $bien->frais_gestion_valeur ?? $bien->gerance?->frais_gestion_valeur ?? 0;
            $commission = $mode === 'pourcentage'
                ? round((float) $paiement->montant * (float) $valeur / 100, 2)
                : (float) $valeur;

            return [
                'paiement' => $paiement,
                'bien' => $bien,
                'bailleur' => $bien->bailleur ?? null,
                'locataire' => $paiement->echeance->contratLocation->location->locataire ?? null,
                'commission' => $commission,
                'net' => round((float) $paiement->montant - $commission, 2),
            ];
        });

        $stats = [
            'total_encaisse' => $lignes->sum(fn ($l) => (float) $l['paiement']->montant),
            'total_commission' => $lignes->sum('commission'),
            'total_net' => $lignes->sum('net'),
        ];

        $bailleurs = Bailleur::orderBy('nom')->get();
        $modesPaiement = ModePaiement::where('actif', true)->orderBy('nom')->get();

        $periodesDisponibles = Paiement::where('statut', 'valide')
            ->selectRaw('YEAR(date_paiement) as annee, MONTH(date_paiement) as mois')
            ->distinct()
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->get()
            ->map(fn ($ligne) => [
                'valeur' => sprintf('%d-%02d', $ligne->annee, $ligne->mois),
                'libelle' => ucfirst(\Carbon\Carbon::createFromDate($ligne->annee, $ligne->mois, 1)->translatedFormat('F Y')),
            ]);

        return view('finance.revenus.index', compact('lignes', 'stats', 'bailleurs', 'modesPaiement', 'periodesDisponibles'));
    }
}
