<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\CategorieDepense;
use Illuminate\Http\Request;

class CategorieDepenseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories_depense,nom'],
            'imputation_defaut' => ['nullable', 'in:bailleur,locataire,agence'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif', true);

        CategorieDepense::create($data);

        return back()->with('success', 'Catégorie de dépense ajoutée avec succès.');
    }

    public function update(Request $request, CategorieDepense $categorieDepense)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories_depense,nom,'.$categorieDepense->id],
            'imputation_defaut' => ['nullable', 'in:bailleur,locataire,agence'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif');

        $categorieDepense->update($data);

        return back()->with('success', 'Catégorie de dépense mise à jour avec succès.');
    }

    public function destroy(CategorieDepense $categorieDepense)
    {
        $categorieDepense->delete();

        return back()->with('success', 'Catégorie de dépense supprimée avec succès.');
    }
}
