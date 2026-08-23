<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Activite;
use App\Models\Commercial\Prospect;
use App\Models\Commercial\Source;

class CommercialDashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'prospects' => Prospect::count(),
            'a_traiter' => Prospect::where('statut', 'non_traite')->count(),
            'en_cours' => Prospect::where('statut', 'en_cours')->count(),
            'gagnes' => Prospect::where('statut', 'gagne')->count(),
            'perdus' => Prospect::where('statut', 'perdu')->count(),
            'annules' => Prospect::where('statut', 'annule')->count(),
        ];

        $tauxConversion = $kpis['prospects'] > 0
            ? round($kpis['gagnes'] / $kpis['prospects'] * 100, 1)
            : 0;

        $sansActivite = Prospect::whereDoesntHave('activites', function ($q) {
            $q->where('date_activite', '>=', now()->subDays(7));
        })
            ->whereNotIn('statut', ['gagne', 'perdu', 'annule'])
            ->count();

        $parSource = Source::withCount('prospects')->get()
            ->filter(fn ($source) => $source->prospects_count > 0)
            ->sortByDesc('prospects_count')
            ->values();

        $activitesRecentes = Activite::with(['prospect', 'utilisateur'])->latest('date_activite')->limit(8)->get();

        $prospectsRecents = Prospect::with(['typeDemande', 'source'])->latest()->limit(6)->get();

        // Évolution des nouveaux prospects sur les 14 derniers jours (pour le graphique de tendance).
        $prospectsParJour = Prospect::selectRaw('DATE(created_at) as jour, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('jour')
            ->pluck('total', 'jour');

        $tendance = collect(range(13, 0))->map(function ($joursAvant) use ($prospectsParJour) {
            $date = now()->subDays($joursAvant)->toDateString();

            return [
                'date' => now()->subDays($joursAvant)->translatedFormat('d M'),
                'total' => $prospectsParJour[$date] ?? 0,
            ];
        });

        return view('dashboard-commercial', compact('kpis', 'tauxConversion', 'sansActivite', 'parSource', 'activitesRecentes', 'prospectsRecents', 'tendance'));
    }
}
