<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\ChargeLocative;
use App\Models\ContratLocation;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;

class ChargeLocativeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'contrat_location_id' => ['required', 'exists:contrats_location,id'],
            'type_charge' => ['required', 'string', 'in:'.implode(',', array_keys(ChargeLocative::TYPES))],
            'titre' => ['required', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'reglee_par_locataire' => ['nullable'],
            'frequence_mois' => ['required', 'integer', 'min:1', 'max:36'],
            'statut' => ['required', 'in:a_payer,payee'],
            'date_charge' => ['required', 'date'],
        ]);

        $contrat = ContratLocation::findOrFail($data['contrat_location_id']);

        ChargeLocative::create([
            'numero' => NumeroService::genererNumero(ChargeLocative::class, 'CHG'),
            'contrat_location_id' => $contrat->id,
            'type_charge' => $data['type_charge'],
            'titre' => $data['titre'],
            'montant' => $data['montant'],
            'description' => $data['description'] ?? null,
            'reglee_par_locataire' => ! empty($data['reglee_par_locataire']),
            'frequence_mois' => $data['frequence_mois'],
            'statut' => $data['statut'],
            'date_charge' => $data['date_charge'],
            'cree_par_id' => auth()->id(),
        ]);

        return redirect()->route('locative.locations.show', $contrat->location_id)->with('success', 'Charge ajoutée avec succès.');
    }

    public function update(Request $request, ChargeLocative $charge)
    {
        $data = $request->validate([
            'statut' => ['required', 'in:a_payer,payee'],
        ]);

        $charge->update($data);

        return back()->with('success', 'Charge mise à jour avec succès.');
    }

    public function destroy(Request $request, ChargeLocative $charge)
    {
        $request->validate(['motif_suppression' => ['required', 'string', 'max:255']]);

        $locationId = $charge->contratLocation->location_id;

        $charge->motif_suppression = $request->motif_suppression;
        $charge->supprime_par_id = auth()->id();
        $charge->save();
        $charge->delete();

        return redirect()->route('locative.locations.show', $locationId)->with('success', 'Charge supprimée avec succès.');
    }
}
