<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Rh\Absence;
use App\Models\Rh\Employe;
use App\Models\Rh\TypeAbsence;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('rh.consulter');

        $absences = Absence::with('employe', 'typeAbsence')
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('employe_id'), fn ($q) => $q->where('employe_id', $request->employe_id))
            ->when($request->filled('type_absence_id'), fn ($q) => $q->where('type_absence_id', $request->type_absence_id))
            ->latest('date_debut')
            ->get();

        $employes = Employe::actifs()->with('poste', 'departement')->orderBy('nom')->get();
        $typesAbsence = TypeAbsence::where('actif', true)->orderBy('nom')->get();

        $stats = [
            'en_attente' => Absence::where('statut', 'en_attente')->count(),
            'validees' => Absence::where('statut', 'validee')->count(),
            'en_cours' => Absence::enCours()->count(),
        ];

        return view('rh.absences.index', compact('absences', 'employes', 'typesAbsence', 'stats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'employe_id' => ['required', 'exists:employes,id'],
            'type_absence_id' => ['required', 'exists:types_absence,id'],
            'date_debut' => ['required', 'date'],
            'date_retour' => ['required', 'date', 'after:date_debut'],
            'motif' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:5120'],
        ]);

        $typeAbsence = TypeAbsence::findOrFail($data['type_absence_id']);
        $dateDebut = \Illuminate\Support\Carbon::parse($data['date_debut']);

        // Une demande doit être faite au moins 3 jours avant la date de début, sauf pour un
        // type marqué "urgence" (ex : Urgence) qui est dispensé de ce délai de préavis.
        if (! $typeAbsence->est_urgence && now()->startOfDay()->diffInDays($dateDebut->copy()->startOfDay(), false) < 3) {
            return back()->withErrors(['date_debut' => 'Une demande doit être faite au moins 3 jours avant la date de début (sauf type « urgence »).'])->withInput();
        }

        $nombreJours = Absence::calculerNombreJours($dateDebut, \Illuminate\Support\Carbon::parse($data['date_retour']));

        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document')->store('absences', 'public');
        }

        $data['numero'] = NumeroService::genererNumeroCourt(Absence::class, 'ABS');
        $data['nombre_jours'] = $nombreJours;
        $data['statut'] = 'en_attente';
        $data['cree_par_id'] = auth()->id();

        Absence::create($data);

        return back()->with('success', 'Demande d\'absence '.$data['numero'].' enregistrée avec succès ('.$nombreJours.' jour(s)).');
    }

    public function changerStatut(Request $request, Absence $absence)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'statut' => ['required', 'string', 'in:'.implode(',', array_keys(Absence::STATUTS))],
            'commentaire_statut' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $absence) {
            $ancienStatut = $absence->statut;
            $nouveauStatut = $data['statut'];

            // Le solde de congé n'est débité qu'au moment où la demande devient "validée", et
            // recrédité si une demande déjà validée est ensuite refusée/annulée — jamais double
            // décompte, jamais de décompte silencieux sur une demande encore en attente.
            if ($nouveauStatut === 'validee' && $ancienStatut !== 'validee') {
                $absence->employe->decrement('solde_conges', $absence->nombre_jours);
                $data['valide_par_id'] = auth()->id();
                $data['date_validation'] = now();
            } elseif ($ancienStatut === 'validee' && $nouveauStatut !== 'validee') {
                $absence->employe->increment('solde_conges', $absence->nombre_jours);
            }

            $absence->update($data);
        });

        return back()->with('success', 'Statut de la demande '.$absence->numero.' mis à jour avec succès.');
    }

    public function apercuDocument(Absence $absence)
    {
        Gate::authorize('rh.consulter');

        abort_unless($absence->document, 404);

        return Storage::disk('public')->response($absence->document);
    }
}
