<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Rh\Poste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PosteController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.gerer');

        $postes = Poste::withCount('employes')->orderBy('nom')->get();

        return view('rh.postes.index', compact('postes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:postes,nom'],
            'fiche_poste' => ['nullable', 'string'],
        ]);

        $data['actif'] = true;

        Poste::create($data);

        return back()->with('success', 'Poste créé avec succès.');
    }

    public function update(Request $request, Poste $poste)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:postes,nom,'.$poste->id],
            'fiche_poste' => ['nullable', 'string'],
            'actif' => ['nullable'],
        ]);

        $data['actif'] = ! empty($data['actif']);

        $poste->update($data);

        return back()->with('success', 'Poste mis à jour avec succès.');
    }
}
