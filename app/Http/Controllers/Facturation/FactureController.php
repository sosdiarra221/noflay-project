<?php

namespace App\Http\Controllers\Facturation;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Prospect;
use App\Models\Facturation\Client;
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
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(fn ($q2) => $q2->where('numero', 'like', "%{$terme}%")
                    ->orWhereHas('client', fn ($q3) => $q3->where('nom_complet', 'like', "%{$terme}%")));
            })
            ->latest()
            ->get();

        $clients = Client::withCount('devis', 'factures')->with('prospect')->orderBy('nom_complet')->get();
        $prospects = Prospect::withCount('activites')->orderByDesc('created_at')->get();

        $stats = [
            'total' => Facture::count(),
            'emise' => Facture::where('statut', 'emise')->count(),
            'payee' => Facture::where('statut', 'payee')->count(),
            'annulee' => Facture::where('statut', 'annulee')->count(),
        ];

        return view('facturation.factures.index', compact('factures', 'clients', 'prospects', 'stats'));
    }

    public function show(Facture $facture)
    {
        $facture->load('client.prospect', 'lignes', 'devisSource', 'creePar');
        $reglage = Reglage::courant();

        return view('facturation.factures.show', compact('facture', 'reglage'));
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
