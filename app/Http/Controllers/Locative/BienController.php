<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\Bien;
use App\Models\CategorieBien;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;

class BienController extends Controller
{
    public function index(Request $request)
    {
        $biens = Bien::with(['bailleur', 'categorie', 'gerance'])
            ->when($request->filled('bailleur_id'), fn ($q) => $q->where('bailleur_id', $request->bailleur_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('type_exploitation'), fn ($q) => $q->where('type_exploitation', $request->type_exploitation))
            ->latest()
            ->get();

        $bailleurs = Bailleur::orderBy('nom')->get();
        $categories = CategorieBien::orderBy('nom')->get();

        return view('locative.biens.index', compact('biens', 'bailleurs', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(Bien::class, 'BIEN');

        Bien::create($data);

        return back()->with('success', 'Bien ajouté avec succès.');
    }

    public function update(Request $request, Bien $bien)
    {
        $data = $this->valider($request);

        $bien->update($data);

        return back()->with('success', 'Bien mis à jour avec succès.');
    }

    public function destroy(Request $request, Bien $bien)
    {
        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $bien->motif_suppression = $request->motif_suppression;
        $bien->supprime_par_id = auth()->id();
        $bien->save();
        $bien->delete();

        return back()->with('success', 'Bien supprimé avec succès.');
    }

    protected function valider(Request $request): array
    {
        $statutsValides = $request->type_exploitation === 'vente' ? Bien::STATUTS_VENTE : Bien::STATUTS_LOCATION;

        return $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'categorie_bien_id' => ['nullable', 'exists:categories_biens,id'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'zone' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'bailleur_id' => ['required', 'exists:bailleurs,id'],
            'gerance_id' => ['nullable', 'exists:contrats_gerance,id'],
            'type_exploitation' => ['required', 'string', 'in:location,vente'],
            'loyer_mensuel' => ['nullable', 'required_if:type_exploitation,location', 'numeric', 'min:0'],
            'prix_vente' => ['nullable', 'required_if:type_exploitation,vente', 'numeric', 'min:0'],
            'frais_gestion_mode' => ['nullable', 'string', 'in:pourcentage,montant_fixe'],
            'frais_gestion_valeur' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['required', 'string', 'in:'.implode(',', $statutsValides)],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
