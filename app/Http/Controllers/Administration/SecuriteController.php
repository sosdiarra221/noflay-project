<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Reglage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SecuriteController extends Controller
{
    public function index()
    {
        Gate::authorize('administration.gerer');

        $reglage = Reglage::courant();

        return view('administration.securite.index', compact('reglage'));
    }

    public function update(Request $request)
    {
        Gate::authorize('administration.gerer');

        $data = $request->validate([
            'duree_inactivite_minutes' => ['required', 'integer', 'min:1', 'max:480'],
        ]);

        Reglage::courant()->update($data);

        return back()->with('success', "Durée d'inactivité avant déconnexion automatique mise à jour.");
    }
}
