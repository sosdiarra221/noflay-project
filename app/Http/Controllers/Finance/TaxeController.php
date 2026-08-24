<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FicheLocative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaxeController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('finance.consulter');

        $annee = $request->filled('annee') ? (int) $request->annee : now()->year;

        $fiches = FicheLocative::where('annee', $annee)->get();

        $parMois = collect(range(1, 12))->map(function ($mois) use ($fiches) {
            $duMois = $fiches->where('mois', $mois);

            return [
                'mois' => $mois,
                'libelle' => ucfirst(\Carbon\Carbon::createFromDate(2026, $mois, 1)->translatedFormat('F')),
                'tva' => (float) $duMois->sum('montant_tva'),
                'tom' => (float) $duMois->sum('montant_tom'),
                'nombre_fiches' => $duMois->count(),
            ];
        });

        $stats = [
            'total_tva' => $parMois->sum('tva'),
            'total_tom' => $parMois->sum('tom'),
            'total_fiches' => $fiches->count(),
        ];

        $anneesDisponibles = FicheLocative::selectRaw('annee')->distinct()->orderByDesc('annee')->pluck('annee');
        if ($anneesDisponibles->isEmpty()) {
            $anneesDisponibles = collect([now()->year]);
        }

        return view('finance.taxes.index', compact('parMois', 'stats', 'annee', 'anneesDisponibles'));
    }
}
