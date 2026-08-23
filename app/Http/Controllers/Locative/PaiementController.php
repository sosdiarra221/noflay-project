<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;

class PaiementController extends Controller
{
    public function pdf(Paiement $paiement)
    {
        $paiement->load(['echeance.contratLocation.bien', 'echeance.contratLocation.location.locataire', 'modePaiement', 'enregistrePar']);

        $pdf = Pdf::loadView('locative.pdf.recu-paiement', compact('paiement'));

        return $pdf->download($paiement->numero.'.pdf');
    }
}
