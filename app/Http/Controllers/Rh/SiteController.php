<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Client;
use App\Models\Rh\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SiteController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.gerer');

        $sites = Site::with('client')->withCount('employes')->orderBy('nom')->get();
        $clients = Client::orderBy('nom_complet')->get();

        return view('rh.sites.index', compact('sites', 'clients'));
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:facturation_clients,id'],
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
            'client_id' => ['nullable', 'exists:facturation_clients,id'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'actif' => ['nullable'],
        ]);

        $data['actif'] = ! empty($data['actif']);

        $site->update($data);

        return back()->with('success', 'Site mis à jour avec succès.');
    }
}
