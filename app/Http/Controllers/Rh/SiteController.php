<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Rh\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SiteController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.gerer');

        $sites = Site::withCount('employes')->orderBy('nom')->get();

        return view('rh.sites.index', compact('sites'));
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
        ]);

        $data['actif'] = true;

        Site::create($data);

        return back()->with('success', 'Site créé avec succès.');
    }

    public function update(Request $request, Site $site)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'actif' => ['nullable'],
        ]);

        $data['actif'] = ! empty($data['actif']);

        $site->update($data);

        return back()->with('success', 'Site mis à jour avec succès.');
    }
}
