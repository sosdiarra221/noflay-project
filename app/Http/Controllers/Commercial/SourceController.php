<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:commercial_sources,nom'],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif', true);

        Source::create($data);

        return back()->with('success', 'Source ajoutée avec succès.');
    }

    public function update(Request $request, Source $source)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:commercial_sources,nom,'.$source->id],
            'actif' => ['nullable', 'boolean'],
        ]);

        $data['actif'] = $request->boolean('actif');

        $source->update($data);

        return back()->with('success', 'Source mise à jour avec succès.');
    }

    public function destroy(Source $source)
    {
        $source->delete();

        return back()->with('success', 'Source supprimée avec succès.');
    }
}
