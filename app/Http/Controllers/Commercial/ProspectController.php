<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Partenaire;
use App\Models\Commercial\Prospect;
use App\Models\Commercial\Source;
use App\Models\Commercial\StatusHistory;
use App\Models\Commercial\TypeDemande;
use App\Models\Locataire;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProspectController extends Controller
{
    public function index(Request $request)
    {
        $prospects = Prospect::with(['typeDemande', 'source', 'partenaire', 'commercial', 'activites'])
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%"));
            })
            ->when($request->filled('type_demande_id'), fn ($q) => $q->where('type_demande_id', $request->type_demande_id))
            ->when($request->filled('source_id'), fn ($q) => $q->where('source_id', $request->source_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->latest()
            ->get();

        $typesDemande = TypeDemande::where('actif', true)->orderBy('nom')->get();
        $sources = Source::where('actif', true)->orderBy('nom')->get();
        $partenaires = Partenaire::where('statut', 'actif')->orderBy('nom')->get();

        return view('commercial.prospects.index', compact('prospects', 'typesDemande', 'sources', 'partenaires'));
    }

    public function show(Prospect $prospect)
    {
        $prospect->load(['typeDemande', 'source', 'commercial', 'activites.utilisateur', 'historiqueStatuts.utilisateur']);

        return view('commercial.prospects.show', compact('prospect'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        if (! $request->boolean('forcer_creation')) {
            $doublon = Prospect::where('telephone', $data['telephone'])
                ->orWhere('email', $data['email'] ?? '__aucun__')
                ->first();

            if ($doublon) {
                $typesDemande = TypeDemande::where('actif', true)->orderBy('nom')->get();
                $sources = Source::where('actif', true)->orderBy('nom')->get();

                return back()->withInput()->with('doublon', $doublon);
            }
        }

        $data['numero'] = NumeroService::genererNumero(Prospect::class, 'PROS');
        $data['statut'] = 'non_traite';
        $data['commercial_id'] = auth()->id();

        $prospect = Prospect::create($data);

        StatusHistory::create([
            'prospect_id' => $prospect->id,
            'ancien_statut' => null,
            'nouveau_statut' => 'non_traite',
            'commentaire' => 'Prospect créé',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('commercial.prospects.show', $prospect)->with('success', 'Prospect créé avec succès.');
    }

    public function update(Request $request, Prospect $prospect)
    {
        $data = $this->valider($request);

        $prospect->update($data);

        return back()->with('success', 'Prospect mis à jour avec succès.');
    }

    public function destroy(Request $request, Prospect $prospect)
    {
        Gate::authorize('commercial.operations-sensibles');

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $prospect->motif_suppression = $request->motif_suppression;
        $prospect->supprime_par_id = auth()->id();
        $prospect->save();
        $prospect->delete();

        return redirect()->route('commercial.prospects.index')->with('success', 'Prospect supprimé avec succès.');
    }

    public function changerStatut(Request $request, Prospect $prospect)
    {
        $data = $request->validate([
            'nouveau_statut' => ['required', 'string', 'in:'.implode(',', Prospect::STATUTS)],
            'commentaire' => ['nullable', 'string', 'max:500'],
        ]);

        $ancienStatut = $prospect->statut;
        $commentaire = trim($data['commentaire'] ?? '') !== '' ? $data['commentaire'] : 'Aucune information renseignée';

        DB::transaction(function () use ($prospect, $data, $ancienStatut, $commentaire) {
            $prospect->update(['statut' => $data['nouveau_statut']]);

            StatusHistory::create([
                'prospect_id' => $prospect->id,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => $data['nouveau_statut'],
                'commentaire' => $commentaire,
                'user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    public function convertirEnLocation(Prospect $prospect)
    {
        if ($prospect->statut !== 'gagne') {
            return back()->with('success', 'Seul un prospect gagné peut être converti.');
        }

        $locataire = Locataire::where('telephone', $prospect->telephone)->first();

        if (! $locataire) {
            $locataire = Locataire::create([
                'numero' => NumeroService::genererNumero(Locataire::class, 'LOCT'),
                'nom' => $prospect->nom,
                'prenom' => $prospect->prenom,
                'telephone' => $prospect->telephone,
                'email' => $prospect->email,
                'adresse' => $prospect->adresse,
                'type_locataire' => 'particulier',
                'statut' => 'actif',
            ]);
        }

        $prospect->update([
            'converti_en' => 'location',
            'converti_le' => now(),
        ]);

        return redirect()->route('locative.locations.create')->with('locataire_cree_id', $locataire->id)
            ->with('success', 'Prospect converti : locataire "'.$locataire->nom_complet.'" prêt à être sélectionné.');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'type_demande_id' => ['nullable', 'exists:commercial_types_demande,id'],
            'besoin' => ['nullable', 'string'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'devise' => ['nullable', 'string', 'max:10'],
            'source_id' => ['nullable', 'exists:commercial_sources,id'],
            'partenaire_id' => ['nullable', 'exists:commercial_partenaires,id'],
        ]);
    }
}
