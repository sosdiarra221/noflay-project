<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaiementController extends Controller
{
    public function pdf(Paiement $paiement)
    {
        $paiement->load(['echeance.contratLocation.bien', 'echeance.contratLocation.location.locataire', 'modePaiement', 'enregistrePar']);

        $pdf = Pdf::loadView('locative.pdf.recu-paiement', compact('paiement'));

        return $pdf->download($paiement->numero.'.pdf');
    }

    public function annuler(Request $request, Paiement $paiement)
    {
        Gate::authorize('locative.operations-sensibles');

        $request->validate([
            'motif_annulation' => ['required', 'string', 'max:255'],
        ]);

        if ($paiement->statut === 'annule') {
            return back()->with('success', 'Ce paiement est déjà annulé.');
        }

        $paiement->motifAction = $request->motif_annulation;
        $paiement->update([
            'statut' => 'annule',
            'motif_annulation' => $request->motif_annulation,
        ]);

        $paiement->echeance->recalculerMontantPaye();

        return back()->with('success', 'Paiement annulé avec succès.');
    }
}
