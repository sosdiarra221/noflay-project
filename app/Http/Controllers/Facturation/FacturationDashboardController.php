<?php

namespace App\Http\Controllers\Facturation;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Devis;

class FacturationDashboardController extends Controller
{
    public function index()
    {
        $devis = Devis::with('client')->latest()->get();

        $stats = [
            'total_devis' => $devis->count(),
            'montant_total_ttc' => (float) $devis->sum('total_ttc'),
            'gagne' => $devis->where('statut', 'gagne')->count(),
            'en_cours' => $devis->whereIn('statut', ['nouveau', 'en_negociation'])->count(),
        ];

        $repartitionStatuts = $devis->groupBy('statut')->map->count();

        $derniersDevis = $devis->take(8);

        return view('facturation.dashboard', compact('stats', 'repartitionStatuts', 'derniersDevis'));
    }
}
