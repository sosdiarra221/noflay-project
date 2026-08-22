<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index()
    {
        $departements = Departement::orderBy('nom')->get();

        return view('departements', compact('departements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:departements,nom'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Departement::create($data);

        return back()->with('success', 'Département créé avec succès.');
    }

    public function update(Request $request, Departement $departement)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:departements,nom,'.$departement->id],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $departement->update($data);

        return back()->with('success', 'Département mis à jour avec succès.');
    }

    public function destroy(Departement $departement)
    {
        $departement->delete();

        return back()->with('success', 'Département supprimé avec succès.');
    }
}
