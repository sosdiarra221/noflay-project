<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Prospect;
use App\Models\Commercial\Source;
use App\Models\Commercial\TypeDemande;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RapportController extends Controller
{
    public function index(Request $request)
    {
        [$debut, $fin] = $this->periode($request);

        $prospects = Prospect::whereBetween('created_at', [$debut, $fin])->get();

        $kpis = [
            'total' => $prospects->count(),
            'gagnes' => $prospects->where('statut', 'gagne')->count(),
            'perdus' => $prospects->where('statut', 'perdu')->count(),
            'en_cours' => $prospects->where('statut', 'en_cours')->count(),
        ];

        $tauxConversion = $kpis['total'] > 0 ? round($kpis['gagnes'] / $kpis['total'] * 100, 1) : 0;

        $parStatut = collect(Prospect::STATUTS)->map(fn ($statut) => [
            'statut' => $statut,
            'total' => $prospects->where('statut', $statut)->count(),
        ]);

        $parSource = Source::withCount(['prospects' => fn ($q) => $q->whereBetween('created_at', [$debut, $fin])])
            ->get()
            ->filter(fn ($s) => $s->prospects_count > 0)
            ->sortByDesc('prospects_count')
            ->values();

        $parTypeDemande = TypeDemande::withCount(['prospects' => fn ($q) => $q->whereBetween('created_at', [$debut, $fin])])
            ->get()
            ->filter(fn ($t) => $t->prospects_count > 0)
            ->sortByDesc('prospects_count')
            ->values();

        $performanceCommerciaux = User::withCount(['prospectsGeres' => fn ($q) => $q->whereBetween('created_at', [$debut, $fin])])
            ->get()
            ->filter(fn ($user) => $user->prospects_geres_count > 0)
            ->map(function ($user) use ($debut, $fin) {
                $gagnes = Prospect::where('commercial_id', $user->id)
                    ->whereBetween('created_at', [$debut, $fin])
                    ->where('statut', 'gagne')
                    ->count();

                return [
                    'nom' => $user->name,
                    'total' => $user->prospects_geres_count,
                    'gagnes' => $gagnes,
                    'taux' => $user->prospects_geres_count > 0 ? round($gagnes / $user->prospects_geres_count * 100, 1) : 0,
                ];
            })
            ->sortByDesc('total')
            ->values();

        return view('commercial.rapports', compact(
            'debut', 'fin', 'kpis', 'tauxConversion', 'parStatut', 'parSource', 'parTypeDemande', 'performanceCommerciaux'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        [$debut, $fin] = $this->periode($request);

        $prospects = Prospect::with(['typeDemande', 'source', 'commercial'])
            ->whereBetween('created_at', [$debut, $fin])
            ->latest()
            ->get();

        $nomFichier = 'rapport-prospects-'.$debut->format('Y-m-d').'-au-'.$fin->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($prospects) {
            $flux = fopen('php://output', 'w');
            fputcsv($flux, ['Numéro', 'Nom', 'Téléphone', 'Email', 'Type de demande', 'Source', 'Statut', 'Commercial', 'Créé le']);

            foreach ($prospects as $prospect) {
                fputcsv($flux, [
                    $prospect->numero,
                    $prospect->nom_complet,
                    $prospect->telephone,
                    $prospect->email,
                    $prospect->typeDemande->nom ?? '',
                    $prospect->source->nom ?? '',
                    $prospect->statut,
                    $prospect->commercial->name ?? '',
                    $prospect->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($flux);
        }, $nomFichier, ['Content-Type' => 'text/csv']);
    }

    protected function periode(Request $request): array
    {
        $debut = $request->filled('debut') ? \Carbon\Carbon::parse($request->debut)->startOfDay() : now()->subDays(29)->startOfDay();
        $fin = $request->filled('fin') ? \Carbon\Carbon::parse($request->fin)->endOfDay() : now()->endOfDay();

        return [$debut, $fin];
    }
}
