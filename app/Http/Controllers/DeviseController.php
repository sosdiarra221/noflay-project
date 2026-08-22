<?php

namespace App\Http\Controllers;

use App\Models\Devise;
use Illuminate\Http\Request;

class DeviseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:devises,code'],
            'nom' => ['required', 'string', 'max:255'],
            'symbole' => ['required', 'string', 'max:20'],
            'est_defaut' => ['nullable', 'boolean'],
        ]);

        $data['est_defaut'] = $request->boolean('est_defaut');

        if ($data['est_defaut']) {
            Devise::query()->update(['est_defaut' => false]);
        }

        Devise::create($data);

        return back()->with('success', 'Devise ajoutée avec succès.');
    }

    public function update(Request $request, Devise $devise)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:devises,code,'.$devise->id],
            'nom' => ['required', 'string', 'max:255'],
            'symbole' => ['required', 'string', 'max:20'],
            'est_defaut' => ['nullable', 'boolean'],
        ]);

        $data['est_defaut'] = $request->boolean('est_defaut');

        if ($data['est_defaut']) {
            Devise::query()->where('id', '!=', $devise->id)->update(['est_defaut' => false]);
        }

        $devise->update($data);

        return back()->with('success', 'Devise mise à jour avec succès.');
    }

    public function destroy(Devise $devise)
    {
        $devise->delete();

        return back()->with('success', 'Devise supprimée avec succès.');
    }
}
