<?php

namespace App\Http\Controllers\Facturation;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Client;
use App\Models\Facturation\Devis;
use App\Models\Reglage;
use App\Services\Locative\NumeroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevisController extends Controller
{
    public function index(Request $request)
    {
        $devis = Devis::with('client', 'lignes')
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('client_id'), fn ($q) => $q->where('client_id', $request->client_id))
            ->latest()
            ->get();

        $clients = Client::orderBy('nom_complet')->get();
        $reglage = Reglage::courant();

        return view('facturation.devis.index', compact('devis', 'clients', 'reglage'));
    }

    public function show(Devis $devis)
    {
        $devis->load('client', 'lignes', 'creePar');

        return view('facturation.devis.show', ['devis' => $devis]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date_devis' => ['required', 'date'],
            'client_id' => ['nullable', 'exists:facturation_clients,id', 'required_without:nouveau_client_nom'],
            'nouveau_client_nom' => ['nullable', 'string', 'max:255', 'required_without:client_id'],
            'nouveau_client_telephone' => ['nullable', 'string', 'max:50'],
            'nouveau_client_email' => ['nullable', 'email', 'max:255'],
            'statut' => ['required', 'string', 'in:'.implode(',', array_keys(Devis::STATUTS))],
            'appliquer_tva' => ['nullable'],
            'taux_tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.quantite' => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['required', 'numeric', 'min:0'],
        ]);

        $devis = DB::transaction(function () use ($data) {
            $client = $data['client_id']
                ? Client::findOrFail($data['client_id'])
                : Client::create([
                    'nom_complet' => $data['nouveau_client_nom'],
                    'telephone' => $data['nouveau_client_telephone'] ?? null,
                    'email' => $data['nouveau_client_email'] ?? null,
                ]);

            $appliquerTva = ! empty($data['appliquer_tva']);
            $tauxTva = $appliquerTva ? (float) ($data['taux_tva'] ?? 0) : 0;

            $sousTotalHt = 0;
            $lignesPreparees = [];
            foreach ($data['lignes'] as $index => $ligne) {
                $total = round((float) $ligne['quantite'] * (float) $ligne['prix_unitaire'], 2);
                $sousTotalHt += $total;
                $lignesPreparees[] = [
                    'designation' => $ligne['designation'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'total' => $total,
                    'ordre' => $index,
                ];
            }

            $montantTva = $appliquerTva ? round($sousTotalHt * $tauxTva / 100, 2) : 0;
            $totalTtc = round($sousTotalHt + $montantTva, 2);

            $devis = Devis::create([
                'numero' => NumeroService::genererNumero(Devis::class, 'DEV'),
                'date_devis' => $data['date_devis'],
                'client_id' => $client->id,
                'statut' => $data['statut'],
                'appliquer_tva' => $appliquerTva,
                'taux_tva' => $tauxTva,
                'sous_total_ht' => $sousTotalHt,
                'montant_tva' => $montantTva,
                'total_ttc' => $totalTtc,
                'notes' => $data['notes'] ?? null,
                'cree_par_id' => auth()->id(),
            ]);

            $devis->lignes()->createMany($lignesPreparees);

            return $devis;
        });

        return redirect()->route('facturation.devis.show', $devis)->with('success', 'Devis créé avec succès.');
    }

    public function update(Request $request, Devis $devis)
    {
        $data = $request->validate([
            'statut' => ['required', 'string', 'in:'.implode(',', array_keys(Devis::STATUTS))],
        ]);

        $devis->update($data);

        return back()->with('success', 'Statut du devis mis à jour avec succès.');
    }

    public function destroy(Request $request, Devis $devis)
    {
        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $devis->motif_suppression = $request->motif_suppression;
        $devis->supprime_par_id = auth()->id();
        $devis->save();
        $devis->delete();

        return redirect()->route('facturation.devis.index')->with('success', 'Devis supprimé avec succès.');
    }

    public function pdf(Devis $devis)
    {
        $devis->load('client', 'lignes');
        $reglage = Reglage::courant();

        $pdf = Pdf::loadView('facturation.pdf.devis', ['devis' => $devis, 'reglage' => $reglage]);

        return $pdf->download($devis->numero.'.pdf');
    }

    public function apercu(Devis $devis)
    {
        $devis->load('client', 'lignes');
        $reglage = Reglage::courant();

        $pdf = Pdf::loadView('facturation.pdf.devis', ['devis' => $devis, 'reglage' => $reglage]);

        return $pdf->stream($devis->numero.'.pdf');
    }
}
