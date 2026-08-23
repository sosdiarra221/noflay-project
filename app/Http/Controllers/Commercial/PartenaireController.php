<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Partenaire;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PartenaireController extends Controller
{
    public function index(Request $request)
    {
        $partenaires = Partenaire::withCount('prospects')
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%"));
            })
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->latest()
            ->get();

        return view('commercial.partenaires.index', compact('partenaires'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(Partenaire::class, 'PART');
        $data['statut'] = $data['statut'] ?? 'actif';

        Partenaire::create($data);

        return back()->with('success', 'Partenaire ajouté avec succès.');
    }

    public function update(Request $request, Partenaire $partenaire)
    {
        $data = $this->valider($request);

        $partenaire->update($data);

        return back()->with('success', 'Partenaire mis à jour avec succès.');
    }

    public function destroy(Request $request, Partenaire $partenaire)
    {
        Gate::authorize('commercial.operations-sensibles');

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $partenaire->motif_suppression = $request->motif_suppression;
        $partenaire->supprime_par_id = auth()->id();
        $partenaire->save();
        $partenaire->delete();

        return back()->with('success', 'Partenaire supprimé avec succès.');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', Partenaire::TYPES)],
            'nom' => ['required', 'string', 'max:255'],
            'contact_nom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'commission_pourcentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'statut' => ['nullable', 'string', 'in:actif,inactif'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
