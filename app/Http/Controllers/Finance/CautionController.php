<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\Caution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CautionController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('finance.consulter');

        $cautions = Caution::with(['contratLocation.bien', 'contratLocation.location.locataire', 'contratLocation.bailleur'])
            ->when($request->filled('bailleur_id'), function ($q) use ($request) {
                $q->whereHas('contratLocation', fn ($q2) => $q2->where('bailleur_id', $request->bailleur_id));
            })
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->latest()
            ->get();

        $bailleurs = Bailleur::orderBy('nom')->get();

        $stats = [
            'total_detenu' => (float) Caution::whereIn('statut', ['detenue', 'partiellement_restituee'])->sum('part_bailleur') - (float) Caution::sum('montant_retenu'),
            'total_retenu' => (float) Caution::sum('montant_retenu'),
            'total_restitue' => (float) Caution::sum('montant_restitue'),
            'nombre_actives' => Caution::whereIn('statut', ['detenue', 'partiellement_restituee'])->count(),
        ];

        return view('finance.cautions.index', compact('cautions', 'bailleurs', 'stats'));
    }

    public function restituer(Request $request, Caution $caution)
    {
        Gate::authorize('finance.gerer');

        $data = $request->validate([
            'montant_retenu' => ['required', 'numeric', 'min:0', 'max:'.(float) $caution->part_bailleur],
            'motif_retenue' => ['required_if:montant_retenu,>0', 'nullable', 'string'],
            'date_restitution' => ['required', 'date'],
        ]);

        $montantRestitue = round((float) $caution->part_bailleur - (float) $data['montant_retenu'], 2);

        $caution->update([
            'montant_retenu' => $data['montant_retenu'],
            'motif_retenue' => $data['motif_retenue'] ?? null,
            'montant_restitue' => $montantRestitue,
            'date_restitution' => $data['date_restitution'],
            'statut' => $data['montant_retenu'] > 0 ? 'partiellement_restituee' : 'restituee',
            'restituee_par_id' => auth()->id(),
        ]);

        if ((float) $data['montant_retenu'] >= (float) $caution->part_bailleur) {
            $caution->update(['statut' => 'restituee']);
        }

        return back()->with('success', 'Caution traitée : '.number_format($montantRestitue, 0, ',', ' ').' FCFA restitués au locataire.');
    }
}
