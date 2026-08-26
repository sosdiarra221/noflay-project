<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Rh\JourFerie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JourFerieController extends Controller
{
    public function index()
    {
        Gate::authorize('administration.gerer');

        $joursFeries = JourFerie::orderBy('date')->get();

        return view('administration.jours-feries.index', compact('joursFeries'));
    }

    public function store(Request $request)
    {
        Gate::authorize('administration.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'recurrent_annuel' => ['nullable'],
        ]);

        $data['recurrent_annuel'] = ! empty($data['recurrent_annuel']);

        JourFerie::create($data);

        return back()->with('success', 'Jour férié ajouté avec succès.');
    }

    public function update(Request $request, JourFerie $joursFerie)
    {
        Gate::authorize('administration.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'recurrent_annuel' => ['nullable'],
        ]);

        $data['recurrent_annuel'] = ! empty($data['recurrent_annuel']);

        $joursFerie->update($data);

        return back()->with('success', 'Jour férié mis à jour avec succès.');
    }

    public function destroy(JourFerie $joursFerie)
    {
        Gate::authorize('administration.gerer');

        $joursFerie->delete();

        return back()->with('success', 'Jour férié supprimé avec succès.');
    }
}
