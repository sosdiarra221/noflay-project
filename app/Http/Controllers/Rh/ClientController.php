<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.gerer');

        $clients = Client::withCount('sites')->orderBy('nom_complet')->get();

        return view('rh.clients.index', compact('clients'));
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
