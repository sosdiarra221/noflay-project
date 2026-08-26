<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Rh\Absence;
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

        $statsConges = [
            'en_attente' => Absence::where('statut', 'en_attente')->count(),
            'validees_mois' => Absence::where('statut', 'validee')->whereMonth('date_debut', now()->month)->whereYear('date_debut', now()->year)->count(),
            'en_cours' => Absence::enCours()->count(),
            'solde_moyen' => round((float) Employe::actifs()->avg('solde_conges'), 1),
        ];

        $absencesEnCours = Absence::enCours()->with('employe', 'typeAbsence')->orderBy('date_retour')->get();

        $employesActifs = Employe::actifs()->with('departement', 'poste')->get();

        $repartitionDepartements = $employesActifs
            ->groupBy(fn ($e) => $e->departement->nom ?? 'Non défini')
            ->map->count();

        $repartitionSexe = $employesActifs
            ->groupBy(fn ($e) => match ($e->sexe) {
                'homme' => 'Homme',
                'femme' => 'Femme',
                default => 'Non renseigné',
            })
            ->map->count();

        $repartitionPoste = $employesActifs
            ->groupBy(fn ($e) => $e->poste->nom ?? 'Non défini')
            ->map->count();

        $contratsEcheanceProche = ContratTravail::with('employe')->echeanceSous(30)->orderBy('date_prevu_fin')->get();

        $derniersEmployes = Employe::with('departement', 'poste')->latest()->take(8)->get();

        return view('rh.dashboard', compact('stats', 'statsConges', 'absencesEnCours', 'repartitionDepartements', 'repartitionSexe', 'repartitionPoste', 'contratsEcheanceProche', 'derniersEmployes'));
    }
}
