<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\ContratLocation;
use App\Models\EcheanceLoyer;
use App\Models\FicheLocative;
use App\Models\Reglage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FicheLocativeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'contrat_location_id' => ['required', 'exists:contrats_location,id'],
            'annee' => ['required', 'integer', 'min:2020', 'max:2100'],
            'mois' => ['required', 'integer', 'min:1', 'max:12'],
            'frais_agence' => ['nullable', 'numeric', 'min:0'],
            'taux_tom' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'taux_tva' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $contrat = ContratLocation::findOrFail($data['contrat_location_id']);

        $debutMois = Carbon::createFromDate($data['annee'], $data['mois'], 1)->startOfDay();

        $arrieres = EcheanceLoyer::where('contrat_location_id', $contrat->id)
            ->where('date_echeance', '<', $debutMois)
            ->whereNotIn('statut', ['paye', 'annule'])
            ->get()
            ->sum(fn ($echeance) => max($echeance->montant_attendu - $echeance->montant_paye, 0));

        $fraisAgence = $data['frais_agence'] ?? 0;
        // La TVA/TOM ne s'applique que si le contrat de location l'a explicitement activé
        // (configuré à la création de la location) : sinon, le taux est forcé à 0 quel que
        // soit ce qui a été saisi dans le formulaire.
        $tauxTom = $contrat->appliquer_tom ? ($data['taux_tom'] ?? 0) : 0;
        $tauxTva = $contrat->appliquer_tva ? ($data['taux_tva'] ?? 0) : 0;
        $montantTom = round($contrat->loyer_mensuel * $tauxTom / 100, 2);
        $montantTva = round($contrat->loyer_mensuel * $tauxTva / 100, 2);
        $montantTotal = $contrat->loyer_mensuel + $arrieres + $fraisAgence + $montantTom + $montantTva;

        $jour = min($contrat->jour_echeance, $debutMois->copy()->daysInMonth);

        $fiche = FicheLocative::where('contrat_location_id', $contrat->id)
            ->where('annee', $data['annee'])
            ->where('mois', $data['mois'])
            ->first();

        $valeurs = [
            'loyer_mensuel' => $contrat->loyer_mensuel,
            'arrieres' => $arrieres,
            'frais_agence' => $fraisAgence,
            'taux_tom' => $tauxTom,
            'montant_tom' => $montantTom,
            'taux_tva' => $tauxTva,
            'montant_tva' => $montantTva,
            'montant_total' => $montantTotal,
            'date_limite_paiement' => Carbon::createFromDate($data['annee'], $data['mois'], $jour),
            'genere_par_id' => auth()->id(),
        ];

        if ($fiche) {
            $fiche->update($valeurs);
        } else {
            $fiche = FicheLocative::create($valeurs + [
                'contrat_location_id' => $contrat->id,
                'annee' => $data['annee'],
                'mois' => $data['mois'],
                'numero_reference' => $this->genererNumero($data['annee'], $data['mois']),
            ]);
        }

        return back()->with('success', 'Fiche proforma « '.$fiche->numero_reference.' » générée avec succès.');
    }

    public function apercu(FicheLocative $fiche)
    {
        [$reglage, $ville, $contrat] = $this->donneesFiche($fiche);

        $pdf = Pdf::loadView('locative.pdf.fiche-proforma', compact('fiche', 'contrat', 'reglage', 'ville'));

        return $pdf->stream($fiche->numero_reference.'.pdf');
    }

    public function telecharger(FicheLocative $fiche)
    {
        [$reglage, $ville, $contrat] = $this->donneesFiche($fiche);

        $pdf = Pdf::loadView('locative.pdf.fiche-proforma', compact('fiche', 'contrat', 'reglage', 'ville'));

        return $pdf->download($fiche->numero_reference.'.pdf');
    }

    protected function donneesFiche(FicheLocative $fiche): array
    {
        $fiche->load('contratLocation.bien', 'contratLocation.location.locataire');
        $contrat = $fiche->contratLocation;

        $reglage = Reglage::courant();
        $ville = $reglage->adresse ? trim(explode(',', $reglage->adresse)[0]) : 'Dakar';

        return [$reglage, $ville, $contrat];
    }

    protected function genererNumero(int $annee, int $mois): string
    {
        $prefixe = sprintf('FP-%d-%02d', $annee, $mois);

        $sequence = FicheLocative::where('annee', $annee)->where('mois', $mois)->count() + 1;

        return sprintf('%s-%03d', $prefixe, $sequence);
    }
}
