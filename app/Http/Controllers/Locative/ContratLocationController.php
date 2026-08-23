<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\ContratLocation;
use App\Models\Documents\Document as DocumentGenere;
use App\Models\ModePaiement;
use App\Services\Locative\EcheanceLoyerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContratLocationController extends Controller
{
    public function show(ContratLocation $contrat)
    {
        $contrat->load(['bien.categorie', 'bailleur', 'location.locataire', 'echeances.paiements', 'modePaiementPrefere']);
        $modesPaiement = ModePaiement::where('actif', true)->orderBy('nom')->get();

        // Document généré par le nouveau moteur de modèles (module Gestion Document), s'il existe,
        // pour proposer la nouvelle expérience Voir/Modifier/PDF à la place du PDF codé en dur.
        $documentGenere = DocumentGenere::where('documentable_type', ContratLocation::class)
            ->where('documentable_id', $contrat->id)
            ->where('status', '!=', DocumentGenere::STATUT_CANCELLED)
            ->latest('generated_at')
            ->first();

        return view('locative.contrats.show', compact('contrat', 'modesPaiement', 'documentGenere'));
    }

    public function update(Request $request, ContratLocation $contrat)
    {
        $data = $request->validate([
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'loyer_mensuel' => ['required', 'numeric', 'min:0'],
            'depot_garantie' => ['nullable', 'numeric', 'min:0'],
            'charges' => ['nullable', 'numeric', 'min:0'],
            'jour_echeance' => ['required', 'integer', 'min:1', 'max:28'],
            'mode_paiement_prefere_id' => ['nullable', 'exists:modes_paiement,id'],
            'statut' => ['required', 'string', 'in:actif,suspendu,expire,resilie,archive'],
            'notes' => ['nullable', 'string'],
        ]);

        $contrat->update($data);

        if (in_array($data['statut'], ['expire', 'resilie'])) {
            $contrat->bien->update(['statut' => 'disponible']);
        }

        return back()->with('success', 'Contrat de location mis à jour avec succès.');
    }

    public function destroy(Request $request, ContratLocation $contrat)
    {
        Gate::authorize('locative.operations-sensibles');

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $contrat->motif_suppression = $request->motif_suppression;
        $contrat->supprime_par_id = auth()->id();
        $contrat->save();
        $contrat->delete();

        $contrat->bien->update(['statut' => 'disponible']);

        return redirect()->route('locative.locations.show', $contrat->location_id)->with('success', 'Contrat de location supprimé avec succès.');
    }

    public function genererLoyers(Request $request, ContratLocation $contrat, EcheanceLoyerService $service)
    {
        $data = $request->validate([
            'annee' => ['required', 'integer', 'min:2000', 'max:2100'],
            'mois' => ['required', 'array', 'min:1'],
            'mois.*' => ['integer', 'min:1', 'max:12'],
        ]);

        $creees = $service->genererLoyersManuel($contrat, $data['annee'], $data['mois']);

        $message = count($creees) > 0
            ? count($creees).' échéance(s) générée(s) avec succès.'
            : 'Aucune nouvelle échéance à générer (déjà existantes).';

        return back()->with('success', $message);
    }

    public function pdf(ContratLocation $contrat)
    {
        $contrat->load(['bien', 'bailleur', 'location.locataire']);

        $pdf = Pdf::loadView('locative.pdf.contrat-location', compact('contrat'));

        return $pdf->download($contrat->numero.'.pdf');
    }

    public function apercu(ContratLocation $contrat)
    {
        $contrat->load(['bien', 'bailleur', 'location.locataire']);

        $pdf = Pdf::loadView('locative.pdf.contrat-location', compact('contrat'));

        return $pdf->stream($contrat->numero.'.pdf');
    }
}
