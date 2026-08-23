<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\EcheanceLoyer;
use App\Models\Paiement;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcheanceLoyerController extends Controller
{
    public function index(Request $request)
    {
        $echeances = EcheanceLoyer::with(['contratLocation.bien', 'contratLocation.location.locataire'])
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('annee'), fn ($q) => $q->where('annee', $request->annee))
            ->when($request->filled('mois'), fn ($q) => $q->where('mois', $request->mois))
            ->orderByDesc('date_echeance')
            ->get();

        return view('locative.echeances.index', compact('echeances'));
    }

    public function encaisser(Request $request, EcheanceLoyer $echeance)
    {
        $data = $request->validate([
            'montant' => ['required', 'numeric', 'min:0.01'],
            'mode_paiement_id' => ['required', 'exists:modes_paiement,id'],
            'date_paiement' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $echeance) {
            Paiement::create([
                'numero' => NumeroService::genererNumero(Paiement::class, 'PAY'),
                'echeance_loyer_id' => $echeance->id,
                'montant' => $data['montant'],
                'mode_paiement_id' => $data['mode_paiement_id'],
                'date_paiement' => $data['date_paiement'],
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
                'enregistre_par_id' => auth()->id(),
            ]);

            $echeance->montant_paye = $echeance->montant_paye + $data['montant'];
            $echeance->recalculerStatut();
        });

        return back()->with('success', 'Paiement enregistré avec succès.');
    }
}
