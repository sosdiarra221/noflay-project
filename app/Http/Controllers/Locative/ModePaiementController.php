<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\ModePaiement;
use Illuminate\Http\Request;

class ModePaiementController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:modes_paiement,nom'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif', true);

        ModePaiement::create($data);

        return back()->with('success', 'Mode de paiement ajouté avec succès.');
    }

    public function update(Request $request, ModePaiement $mode)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:modes_paiement,nom,'.$mode->id],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif');

        $mode->update($data);

        return back()->with('success', 'Mode de paiement mis à jour avec succès.');
    }

    public function destroy(ModePaiement $mode)
    {
        $mode->delete();

        return back()->with('success', 'Mode de paiement supprimé avec succès.');
    }
}
