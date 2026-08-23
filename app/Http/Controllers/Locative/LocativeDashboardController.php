<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\Bien;
use App\Models\ContratGerance;
use App\Models\ContratLocation;
use App\Models\EcheanceLoyer;

class LocativeDashboardController extends Controller
{
    public function index()
    {
        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();

        $kpis = [
            'bailleurs_actifs' => Bailleur::where('statut', 'actif')->count(),
            'biens_geres' => Bien::count(),
            'biens_disponibles' => Bien::where('statut', 'disponible')->count(),
            'biens_occupes' => Bien::where('statut', 'occupe')->count(),
            'loyers_attendus' => EcheanceLoyer::whereBetween('date_echeance', [$debutMois, $finMois])->sum('montant_attendu'),
            'loyers_encaisses' => EcheanceLoyer::whereBetween('date_echeance', [$debutMois, $finMois])->sum('montant_paye'),
            'impayes' => EcheanceLoyer::whereIn('statut', ['en_retard', 'partiellement_paye'])->get()->sum(fn ($e) => $e->montant_attendu - $e->montant_paye),
        ];

        $alertes = [
            'loyers_en_retard' => EcheanceLoyer::with(['contratLocation.bien', 'contratLocation.location.locataire'])
                ->where('statut', 'en_retard')
                ->orderBy('date_echeance')
                ->limit(10)
                ->get(),
            'gerances_expirant' => ContratGerance::with('bailleur')
                ->where('statut', 'actif')
                ->whereNotNull('date_fin')
                ->whereBetween('date_fin', [now(), now()->addDays(30)])
                ->orderBy('date_fin')
                ->get(),
            'locations_expirant' => ContratLocation::with(['bien', 'location.locataire'])
                ->where('statut', 'actif')
                ->whereNotNull('date_fin')
                ->whereBetween('date_fin', [now(), now()->addDays(30)])
                ->orderBy('date_fin')
                ->get(),
            'biens_vacants' => Bien::where('statut', 'disponible')
                ->where('updated_at', '<=', now()->subDays(60))
                ->limit(10)
                ->get(),
        ];

        return view('locative.dashboard', compact('kpis', 'alertes'));
    }
}
