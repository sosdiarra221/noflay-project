<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Rh\ContratTravail;
use App\Models\Rh\Employe;
use Illuminate\Support\Facades\Gate;

class RhDashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.consulter');

        $stats = [
            'total_actifs' => Employe::actifs()->count(),
            'total_sortis' => Employe::where('statut', 'sortie')->count(),
            'contrats_actifs' => ContratTravail::actifs()->count(),
            'sans_contrat_actif' => Employe::actifs()->whereDoesntHave('contrats', fn ($q) => $q->where('etat', 'actif'))->count(),
        ];

        $repartitionDepartements = Employe::actifs()->with('departement')->get()
            ->groupBy(fn ($e) => $e->departement->nom ?? 'Non défini')
            ->map->count();

        $contratsEcheanceProche = ContratTravail::with('employe')->echeanceSous(30)->orderBy('date_prevu_fin')->get();

        $derniersEmployes = Employe::with('departement', 'poste')->latest()->take(8)->get();

        return view('rh.dashboard', compact('stats', 'repartitionDepartements', 'contratsEcheanceProche', 'derniersEmployes'));
    }
}
