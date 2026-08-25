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
            'accepte' => $devis->where('statut', 'accepte')->count(),
            'en_attente' => $devis->whereIn('statut', ['brouillon', 'envoye'])->count(),
        ];

        $repartitionStatuts = $devis->groupBy('statut')->map->count();

        $derniersDevis = $devis->take(8);

        return view('facturation.dashboard', compact('stats', 'repartitionStatuts', 'derniersDevis'));
    }
}
