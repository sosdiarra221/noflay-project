<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Prospect;
use App\Models\Commercial\RendezVous;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    protected array $couleurs = [
        'rendez_vous' => '#405189',
        'visite' => '#0ab39c',
        'appel' => '#f7b84b',
        'autre' => '#299cdb',
    ];

    public function index()
    {
        $rendezVous = RendezVous::with('prospect')->orderByDesc('date_debut')->get();

        $evenements = $rendezVous->map(function (RendezVous $rdv) {
            return [
                'id' => $rdv->id,
                'title' => $rdv->titre,
                'start' => $rdv->date_debut->toIso8601String(),
                'end' => $rdv->date_fin?->toIso8601String(),
                'color' => $this->couleurs[$rdv->type] ?? '#405189',
                'classNames' => $rdv->statut === 'annule' ? ['opacity-50', 'text-decoration-line-through'] : [],
                'extendedProps' => [
                    'type' => $rdv->type,
                    'statut' => $rdv->statut,
                    'lieu' => $rdv->lieu,
                    'description' => $rdv->description,
                    'prospect_id' => $rdv->prospect_id,
                    'prospect_nom' => $rdv->prospect?->nom_complet,
                ],
            ];
        });

        $prospects = Prospect::orderBy('nom')->get(['id', 'nom', 'prenom']);
        $aVenir = RendezVous::with('prospect')->where('statut', 'planifie')->where('date_debut', '>=', now())->orderBy('date_debut')->limit(6)->get();

        return view('commercial.agenda', compact('evenements', 'prospects', 'aVenir'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:rendez_vous,visite,appel,autre'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['nullable', 'string', 'in:planifie,termine,annule'],
            'prospect_id' => ['nullable', 'exists:prospects,id'],
        ]);

        $data['user_id'] = auth()->id();
        $data['statut'] = $data['statut'] ?? 'planifie';

        $rdv = RendezVous::create($data);

        if ($rdv->prospect_id) {
            $rdv->prospect->activites()->create([
                'type' => $rdv->type === 'visite' ? 'visite' : 'rendez_vous',
                'objet' => $rdv->titre,
                'description' => $rdv->description,
                'date_activite' => $rdv->date_debut,
                'user_id' => auth()->id(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $rdv->id]);
        }

        return back()->with('success', 'Rendez-vous ajouté avec succès.');
    }

    public function update(Request $request, RendezVous $rendezVous)
    {
        $data = $request->validate([
            'titre' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'string', 'in:rendez_vous,visite,appel,autre'],
            'date_debut' => ['sometimes', 'required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['nullable', 'string', 'in:planifie,termine,annule'],
            'prospect_id' => ['nullable', 'exists:prospects,id'],
        ]);

        $rendezVous->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Rendez-vous mis à jour avec succès.');
    }

    public function destroy(Request $request, RendezVous $rendezVous)
    {
        $rendezVous->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Rendez-vous supprimé avec succès.');
    }
}
