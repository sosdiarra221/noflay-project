<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\ContratLocation;
use App\Models\Documents\Document as DocumentGenere;
use App\Models\Locative\ParametreLocative;
use App\Models\ModePaiement;
use App\Services\Documents\DocumentGenerationService;
use App\Services\Locative\EcheanceLoyerService;
use App\Services\Locative\NumeroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ContratLocationController extends Controller
{
    public function show(ContratLocation $contrat)
    {
        $contrat->load(['bien.categorie', 'bailleur', 'location.locataire', 'echeances.paiements', 'modePaiementPrefere', 'renouveleDepuis', 'renouveleVers']);
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
        Gate::authorize('locative.contrats.modifier');

        $data = $request->validate([
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'loyer_mensuel' => ['required', 'numeric', 'min:0'],
            'depot_garantie' => ['nullable', 'numeric', 'min:0'],
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

    /**
     * Suspend un contrat actif (le bien reste occupé — seul le suivi des loyers est mis en
     * pause) ou réactive un contrat suspendu.
     */
    public function suspendre(ContratLocation $contrat)
    {
        Gate::authorize('locative.contrats.statut');

        if ($contrat->statut === 'suspendu') {
            $contrat->update(['statut' => 'actif']);

            return back()->with('success', 'Contrat réactivé avec succès.');
        }

        $contrat->update(['statut' => 'suspendu']);

        return back()->with('success', 'Contrat suspendu avec succès.');
    }

    /**
     * Résilie définitivement le contrat entre le locataire et l'agence : date effective et motif
     * obligatoires, le bien redevient disponible à la location. La trace (motif, date, auteur)
     * reste consultable dans le journal d'activité — le contrat n'est jamais supprimé, il passe
     * simplement au statut "résilié" et devient un dossier archivé.
     */
    public function resilier(Request $request, ContratLocation $contrat)
    {
        Gate::authorize('locative.contrats.statut');

        $data = $request->validate([
            'date_fin' => ['required', 'date'],
            'motif_resiliation' => ['required', 'string'],
        ]);

        $contrat->update([
            'statut' => 'resilie',
            'date_fin' => $data['date_fin'],
            'motif_resiliation' => $data['motif_resiliation'],
        ]);

        $contrat->bien->update(['statut' => 'disponible']);

        return redirect()->route('locative.contrats.show', $contrat)->with('success', 'Contrat résilié avec succès. Le dossier est archivé et le bien redevient disponible.');
    }

    /**
     * Renouvelle un contrat : l'ancien passe au statut "expiré" (superseded) et un nouveau
     * contrat de location est créé pour le même bien/bailleur/location, avec les nouvelles dates
     * et le nouveau loyer indiqués, échéances générées automatiquement pour la nouvelle période.
     */
    public function renouveler(Request $request, ContratLocation $contrat, EcheanceLoyerService $echeanceService, DocumentGenerationService $documentGenerationService)
    {
        Gate::authorize('locative.contrats.statut');

        $data = $request->validate([
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'loyer_mensuel' => ['required', 'numeric', 'min:0'],
        ]);

        $indexSuivant = ContratLocation::where('location_id', $contrat->location_id)->count() + 1;

        $nouveauContrat = ContratLocation::create([
            'numero' => NumeroService::genererNumeroBail($contrat->location->numero, $indexSuivant),
            'location_id' => $contrat->location_id,
            'bien_id' => $contrat->bien_id,
            'bailleur_id' => $contrat->bailleur_id,
            'type_location' => $contrat->type_location,
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'] ?? null,
            'loyer_mensuel' => $data['loyer_mensuel'],
            'depot_garantie' => $contrat->depot_garantie,
            'depot_garantie_part_bailleur' => $contrat->depot_garantie_part_bailleur,
            'depot_garantie_part_agence' => $contrat->depot_garantie_part_agence,
            'jour_echeance' => $contrat->jour_echeance,
            'appliquer_tva' => $contrat->appliquer_tva,
            'appliquer_tom' => $contrat->appliquer_tom,
            'mode_paiement_prefere_id' => $contrat->mode_paiement_prefere_id,
            'statut' => 'actif',
            'notes' => 'Renouvellement du contrat '.$contrat->numero.'.',
            'renouvele_depuis_id' => $contrat->id,
        ]);

        $contrat->update(['statut' => 'expire']);
        $contrat->bien->update(['statut' => 'occupe']);

        $echeanceService->genererPourContrat($nouveauContrat);

        try {
            $type = $documentGenerationService->typePourContratLocation($nouveauContrat);
            $documentGenerationService->generateFor($nouveauContrat, $type);
        } catch (\Throwable $e) {
            Log::warning('Échec de génération automatique du document contractuel pour le renouvellement '.$nouveauContrat->numero.' : '.$e->getMessage());
        }

        return redirect()->route('locative.contrats.show', $nouveauContrat)->with('success', 'Contrat renouvelé avec succès sous le numéro '.$nouveauContrat->numero.'.');
    }

    public function destroy(Request $request, ContratLocation $contrat)
    {
        Gate::authorize('locative.contrats.supprimer');

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
        Gate::authorize('locative.contrats.modifier');

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
        $reglage = ParametreLocative::courant();

        $pdf = Pdf::loadView('locative.pdf.contrat-location', compact('contrat', 'reglage'));

        return $pdf->download($contrat->numero.'.pdf');
    }

    public function apercu(ContratLocation $contrat)
    {
        $contrat->load(['bien', 'bailleur', 'location.locataire']);
        $reglage = ParametreLocative::courant();

        $pdf = Pdf::loadView('locative.pdf.contrat-location', compact('contrat', 'reglage'));

        return $pdf->stream($contrat->numero.'.pdf');
    }
}
