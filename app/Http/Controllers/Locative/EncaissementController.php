<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Locataire;
use App\Models\ModePaiement;
use App\Models\Paiement;
use Illuminate\Http\Request;

class EncaissementController extends Controller
{
    public function index(Request $request)
    {
        $paiements = Paiement::with(['echeance.contratLocation.bien', 'echeance.contratLocation.location.locataire', 'modePaiement', 'enregistrePar'])
            ->when($request->filled('locataire_id'), function ($q) use ($request) {
                $q->whereHas('echeance.contratLocation.location', fn ($q2) => $q2->where('locataire_id', $request->locataire_id));
            })
            ->when($request->filled('mode_paiement_id'), fn ($q) => $q->where('mode_paiement_id', $request->mode_paiement_id))
            ->when($request->filled('periode'), function ($q) use ($request) {
                [$debut, $fin] = $this->plagePeriode($request->periode);
                $q->whereBetween('date_paiement', [$debut, $fin]);
            })
            ->when(! $request->filled('periode') && $request->filled('mois_annee'), function ($q) use ($request) {
                [$annee, $mois] = explode('-', $request->mois_annee);
                $q->whereYear('date_paiement', $annee)->whereMonth('date_paiement', $mois);
            })
            ->orderByDesc('date_paiement')
            ->get();

        $stats = [
            'total' => $paiements->where('statut', 'valide')->sum('montant'),
            'nombre' => $paiements->where('statut', 'valide')->count(),
            'moyenne' => $paiements->where('statut', 'valide')->count() > 0
                ? $paiements->where('statut', 'valide')->avg('montant')
                : 0,
            'annules' => $paiements->where('statut', 'annule')->count(),
        ];

        $locataires = Locataire::orderBy('nom')->get();
        $modesPaiement = ModePaiement::where('actif', true)->orderBy('nom')->get();

        $periodesDisponibles = Paiement::selectRaw('YEAR(date_paiement) as annee, MONTH(date_paiement) as mois')
            ->distinct()
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->get()
            ->map(fn ($ligne) => [
                'valeur' => sprintf('%d-%02d', $ligne->annee, $ligne->mois),
                'libelle' => ucfirst(\Carbon\Carbon::createFromDate($ligne->annee, $ligne->mois, 1)->translatedFormat('F Y')),
            ]);

        return view('locative.encaissements.index', compact('paiements', 'stats', 'locataires', 'modesPaiement', 'periodesDisponibles'));
    }

    protected function plagePeriode(string $periode): array
    {
        return match ($periode) {
            'aujourdhui' => [now()->startOfDay(), now()->endOfDay()],
            'hier' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'cette_semaine' => [now()->startOfWeek(), now()->endOfWeek()],
            'ce_mois' => [now()->startOfMonth(), now()->endOfMonth()],
            'cette_annee' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
