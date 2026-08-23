<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;

class BailleurController extends Controller
{
    public function index()
    {
        $bailleurs = Bailleur::withCount(['gerances', 'biens'])->orderBy('nom')->get();

        return view('locative.bailleurs.index', compact('bailleurs'));
    }

    public function show(Bailleur $bailleur)
    {
        $bailleur->load(['gerances' => fn ($q) => $q->latest(), 'biens' => fn ($q) => $q->latest()]);

        return view('locative.bailleurs.show', compact('bailleur'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(Bailleur::class, 'BLR');

        $bailleur = Bailleur::create($data);

        return back()->with('success', 'Bailleur créé avec succès.')->with('bailleur_cree_id', $bailleur->id);
    }

    public function update(Request $request, Bailleur $bailleur)
    {
        $data = $this->valider($request);

        $bailleur->update($data);

        return back()->with('success', 'Bailleur mis à jour avec succès.');
    }

    public function destroy(Request $request, Bailleur $bailleur)
    {
        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $bailleur->motif_suppression = $request->motif_suppression;
        $bailleur->supprime_par_id = auth()->id();
        $bailleur->save();
        $bailleur->delete();

        return redirect()->route('locative.bailleurs.index')->with('success', 'Bailleur supprimé avec succès.');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'in:particulier,entreprise'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'piece_identite_type' => ['nullable', 'string', 'max:100'],
            'piece_identite_numero' => ['nullable', 'string', 'max:100'],
            'ninea' => ['nullable', 'string', 'max:100'],
            'coordonnees_paiement' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'statut' => ['required', 'string', 'in:actif,inactif'],
        ]);
    }
}
