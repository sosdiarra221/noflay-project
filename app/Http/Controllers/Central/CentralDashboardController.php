<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Licence;
use App\Models\Package;
use App\Models\Tenant;
use Illuminate\Support\Carbon;

class CentralDashboardController extends Controller
{
    public function index()
    {
        // Dernière licence de chaque société (celle dont la date de fin est la plus tardive fait foi).
        $dernieresLicences = Licence::all()
            ->groupBy('tenant_id')
            ->map(fn ($licences) => $licences->sortByDesc('date_fin')->first());

        $stats = [
            'total_societes' => Tenant::count(),
            'societes_actives' => Tenant::where('statut', 'actif')->count(),
            'societes_suspendues' => Tenant::where('statut', 'suspendu')->count(),
            'packages_actifs' => Package::where('actif', true)->count(),
            'licences_expirant_bientot' => $dernieresLicences->filter(fn ($l) => ! $l->estExpiree() && $l->joursRestants() <= 5)->count(),
            'licences_expirees' => $dernieresLicences->filter(fn ($l) => $l->estExpiree())->count(),
        ];

        $licencesAExaminer = $dernieresLicences
            ->filter(fn ($l) => $l->estExpiree() || $l->joursRestants() <= 5)
            ->sortBy('date_fin')
            ->take(10)
            ->load('tenant', 'package');

        $societes = Tenant::with('domains')->get()->keyBy('id');

        return view('central.dashboard', compact('stats', 'licencesAExaminer', 'societes'));
    }
}
