<?php

namespace App\Http\Controllers\Facturation;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Facture;
use App\Models\Reglage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $factures = Facture::with('client', 'lignes')
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->latest()
            ->get();

        return view('facturation.factures.index', compact('factures'));
    }

    public function show(Facture $facture)
    {
        $facture->load('client', 'lignes', 'devisSource', 'creePar');

        return view('facturation.factures.show', compact('facture'));
    }

    public function update(Request $request, Facture $facture)
    {
        $data = $request->validate([
            'statut' => ['required', 'string', 'in:'.implode(',', array_keys(Facture::STATUTS))],
        ]);

        $facture->update($data);

        return back()->with('success', 'Statut de la facture mis à jour avec succès.');
    }

    public function pdf(Facture $facture)
    {
        $facture->load('client', 'lignes');
        $reglage = Reglage::courant();

        $pdf = Pdf::loadView('facturation.pdf.facture', compact('facture', 'reglage'));

        return $pdf->download(str_replace('/', '-', $facture->numero).'.pdf');
    }

    public function apercu(Facture $facture)
    {
        $facture->load('client', 'lignes');
        $reglage = Reglage::courant();

        $pdf = Pdf::loadView('facturation.pdf.facture', compact('facture', 'reglage'));

        return $pdf->stream(str_replace('/', '-', $facture->numero).'.pdf');
    }
}
