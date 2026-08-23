<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BailleurController extends Controller
{
    public function index(Request $request)
    {
        $bailleurs = Bailleur::withCount(['gerances', 'biens'])
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%"));
            })
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->orderBy('nom')
            ->get();

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
        Gate::authorize('locative.operations-sensibles');

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
