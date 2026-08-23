<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Locataire;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;

class LocataireController extends Controller
{
    public function index()
    {
        $locataires = Locataire::withCount('locations')->orderBy('nom')->get();

        return view('locative.locataires.index', compact('locataires'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(Locataire::class, 'LOCT');

        $locataire = Locataire::create($data);

        return back()->with('success', 'Locataire créé avec succès.')->with('locataire_cree_id', $locataire->id);
    }

    public function update(Request $request, Locataire $locataire)
    {
        $data = $this->valider($request);

        $locataire->update($data);

        return back()->with('success', 'Locataire mis à jour avec succès.');
    }

    public function destroy(Request $request, Locataire $locataire)
    {
        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $locataire->motif_suppression = $request->motif_suppression;
        $locataire->supprime_par_id = auth()->id();
        $locataire->save();
        $locataire->delete();

        return redirect()->route('locative.locataires.index')->with('success', 'Locataire supprimé avec succès.');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'type_locataire' => ['nullable', 'string', 'in:particulier,entreprise'],
            'piece_identite_type' => ['nullable', 'string', 'max:100'],
            'piece_identite_numero' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'statut' => ['required', 'string', 'in:actif,inactif'],
        ]);
    }
}
