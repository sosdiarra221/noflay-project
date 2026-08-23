<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\CategorieBien;
use Illuminate\Http\Request;

class CategorieBienController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories_biens,nom'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif', true);

        CategorieBien::create($data);

        return back()->with('success', 'Catégorie de bien ajoutée avec succès.');
    }

    public function update(Request $request, CategorieBien $categorie)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories_biens,nom,'.$categorie->id],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif');

        $categorie->update($data);

        return back()->with('success', 'Catégorie de bien mise à jour avec succès.');
    }

    public function destroy(CategorieBien $categorie)
    {
        $categorie->delete();

        return back()->with('success', 'Catégorie de bien supprimée avec succès.');
    }
}
