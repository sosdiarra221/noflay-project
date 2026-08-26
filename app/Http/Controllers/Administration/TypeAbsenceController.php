<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Rh\TypeAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TypeAbsenceController extends Controller
{
    public function index()
    {
        Gate::authorize('administration.gerer');

        $typesAbsence = TypeAbsence::withCount('absences')->orderBy('nom')->get();

        return view('administration.types-absence.index', compact('typesAbsence'));
    }

    public function store(Request $request)
    {
        Gate::authorize('administration.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_absence,nom'],
            'est_urgence' => ['nullable'],
        ]);

        $data['est_urgence'] = ! empty($data['est_urgence']);
        $data['actif'] = true;

        TypeAbsence::create($data);

        return back()->with('success', "Type d'absence créé avec succès.");
    }

    public function update(Request $request, TypeAbsence $typesAbsence)
    {
        Gate::authorize('administration.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_absence,nom,'.$typesAbsence->id],
            'est_urgence' => ['nullable'],
            'actif' => ['nullable'],
        ]);

        $data['est_urgence'] = ! empty($data['est_urgence']);
        $data['actif'] = ! empty($data['actif']);

        $typesAbsence->update($data);

        return back()->with('success', "Type d'absence mis à jour avec succès.");
    }
}
