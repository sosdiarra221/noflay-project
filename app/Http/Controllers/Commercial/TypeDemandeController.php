<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\TypeDemande;
use Illuminate\Http\Request;

class TypeDemandeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:commercial_types_demande,nom'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif', true);

        TypeDemande::create($data);

        return back()->with('success', 'Type de demande ajouté avec succès.');
    }

    public function update(Request $request, TypeDemande $type)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:commercial_types_demande,nom,'.$type->id],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif');

        $type->update($data);

        return back()->with('success', 'Type de demande mis à jour avec succès.');
    }

    public function destroy(TypeDemande $type)
    {
        $type->delete();

        return back()->with('success', 'Type de demande supprimé avec succès.');
    }
}
