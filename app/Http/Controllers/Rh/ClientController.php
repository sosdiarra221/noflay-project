<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('rh.gerer');

        $tousClients = Client::withCount('sites')->orderBy('nom_complet')->get();

        $clients = $tousClients
            ->when($request->filled('client_id'), fn ($q) => $q->where('id', (int) $request->client_id));

        $stats = [
            'total' => $tousClients->count(),
            'avec_sites' => $tousClients->filter(fn ($c) => $c->sites_count > 0)->count(),
            'sans_site' => $tousClients->filter(fn ($c) => $c->sites_count === 0)->count(),
            'total_sites' => $tousClients->sum('sites_count'),
        ];

        return view('rh.clients.index', compact('clients', 'tousClients', 'stats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom_complet' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Client::create($data);

        return back()->with('success', 'Client créé avec succès.');
    }
}
