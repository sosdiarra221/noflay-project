<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Reglage;
use App\Models\Rh\ContratTravail;
use App\Models\Rh\Employe;
use App\Services\Locative\NumeroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ContratTravailController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('rh.consulter');

        $contrats = ContratTravail::with('employe.departement')
            ->when($request->filled('etat'), fn ($q) => $q->where('etat', $request->etat))
            ->when($request->filled('echeance'), fn ($q) => $q->echeanceSous((int) $request->echeance))
            ->when($request->filled('employe_id'), fn ($q) => $q->where('employe_id', $request->employe_id))
            ->latest('date_debut')
            ->get();

        $employes = Employe::orderBy('nom')->get();

        return view('rh.contrats.index', compact('contrats', 'employes'));
    }

    public function store(Request $request, Employe $employe)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'type_contrat' => ['required', 'string', 'in:'.implode(',', array_keys(ContratTravail::TYPES))],
            'date_debut' => ['required', 'date'],
            'date_prevu_fin' => ['nullable', 'date', 'after:date_debut'],
            'montant' => ['nullable', 'numeric', 'min:0'],
            'motif' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document')->store('contrats-travail', 'public');
        }

        $data['numero'] = NumeroService::genererNumeroCourt(ContratTravail::class, 'CT');
        $data['employe_id'] = $employe->id;
        $data['etat'] = 'actif';
        $data['cree_par_id'] = auth()->id();

        $employe->contrats()->create($data);

        return back()->with('success', 'Contrat de travail enregistré avec succès.');
    }

    public function renouveler(Request $request, ContratTravail $contrat)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'type_contrat' => ['required', 'string', 'in:'.implode(',', array_keys(ContratTravail::TYPES))],
            'date_debut' => ['required', 'date'],
            'date_prevu_fin' => ['nullable', 'date', 'after:date_debut'],
            'montant' => ['nullable', 'numeric', 'min:0'],
        ]);

        $contrat->update(['etat' => 'cloture', 'date_fin' => $data['date_debut']]);

        $nouveauContrat = $contrat->employe->contrats()->create([
            'numero' => NumeroService::genererNumeroCourt(ContratTravail::class, 'CT'),
            'type_contrat' => $data['type_contrat'],
            'date_debut' => $data['date_debut'],
            'date_prevu_fin' => $data['date_prevu_fin'] ?? null,
            'montant' => $data['montant'] ?? $contrat->montant,
            'etat' => 'actif',
            'contrat_precedent_id' => $contrat->id,
            'cree_par_id' => auth()->id(),
        ]);

        return back()->with('success', 'Contrat renouvelé avec succès sous le numéro '.$nouveauContrat->numero.'.');
    }

    public function cloturer(Request $request, ContratTravail $contrat)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'date_fin' => ['required', 'date'],
            'motif' => ['required', 'string'],
        ]);

        $contrat->update(['etat' => 'cloture', 'date_fin' => $data['date_fin'], 'motif' => $data['motif']]);

        return back()->with('success', 'Contrat clôturé avec succès.');
    }

    public function apercuDocument(ContratTravail $contrat)
    {
        Gate::authorize('rh.consulter');

        abort_unless($contrat->document, 404);

        return Storage::disk('public')->response($contrat->document);
    }

    public function pdf(ContratTravail $contrat)
    {
        Gate::authorize('rh.consulter');

        $contrat->load('employe.departement', 'employe.poste');
        $reglage = Reglage::courant();

        $pdf = Pdf::loadView('rh.pdf.contrat-travail', ['contrat' => $contrat, 'reglage' => $reglage]);

        return $pdf->download(str_replace('/', '-', $contrat->numero).'.pdf');
    }

    public function apercuPdf(ContratTravail $contrat)
    {
        Gate::authorize('rh.consulter');

        $contrat->load('employe.departement', 'employe.poste');
        $reglage = Reglage::courant();

        $pdf = Pdf::loadView('rh.pdf.contrat-travail', ['contrat' => $contrat, 'reglage' => $reglage]);

        return $pdf->stream(str_replace('/', '-', $contrat->numero).'.pdf');
    }
}
