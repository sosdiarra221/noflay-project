<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\CategorieBien;
use App\Models\ContratGerance;
use App\Services\Locative\NumeroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContratGeranceController extends Controller
{
    public function index()
    {
        $gerances = ContratGerance::with('bailleur')->withCount('biens')->latest()->get();

        return view('locative.gerances.index', compact('gerances'));
    }

    public function show(ContratGerance $gerance)
    {
        $gerance->load(['bailleur', 'biens.categorie']);
        $categories = CategorieBien::where('actif', true)->orderBy('nom')->get();

        return view('locative.gerances.show', compact('gerance', 'categories'));
    }

    public function create()
    {
        $bailleurs = Bailleur::where('statut', 'actif')->orderBy('nom')->get();

        return view('locative.gerances.create', compact('bailleurs'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(ContratGerance::class, 'GER');

        $gerance = ContratGerance::create($data);

        return redirect()->route('locative.gerances.show', $gerance)->with('success', 'Contrat de gérance créé avec succès.');
    }

    public function update(Request $request, ContratGerance $gerance)
    {
        $data = $this->valider($request);

        $champsFinanciers = ['frais_gestion_mode', 'frais_gestion_valeur', 'tva_charge', 'taxe_charge', 'tom_charge'];
        $modifieFinancier = collect($champsFinanciers)->contains(fn ($champ) => (string) $gerance->{$champ} !== (string) $data[$champ]);

        if ($gerance->statut === 'actif' && $modifieFinancier) {
            Gate::authorize('locative.operations-sensibles');

            $request->validate([
                'motif' => ['required', 'string', 'max:255'],
            ]);

            $gerance->motifAction = $request->motif;
        }

        $gerance->update($data);

        return back()->with('success', 'Contrat de gérance mis à jour avec succès.');
    }

    public function destroy(Request $request, ContratGerance $gerance)
    {
        Gate::authorize('locative.operations-sensibles');

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $gerance->motif_suppression = $request->motif_suppression;
        $gerance->supprime_par_id = auth()->id();
        $gerance->save();
        $gerance->delete();

        return redirect()->route('locative.gerances.index')->with('success', 'Contrat de gérance supprimé avec succès.');
    }

    public function pdf(ContratGerance $gerance)
    {
        $gerance->load(['bailleur', 'biens']);

        $pdf = Pdf::loadView('locative.pdf.gerance', compact('gerance'));

        return $pdf->download($gerance->numero.'.pdf');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'bailleur_id' => ['required', 'exists:bailleurs,id'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'type_gerance' => ['required', 'string', 'in:gestion_locative,gestion_vente,gestion_locative_vente'],
            'frais_gestion_mode' => ['required', 'string', 'in:pourcentage,montant_fixe'],
            'frais_gestion_valeur' => ['required', 'numeric', 'min:0'],
            'tva_charge' => ['required', 'string', 'in:bailleur,agence'],
            'taxe_charge' => ['required', 'string', 'in:bailleur,agence'],
            'tom_charge' => ['required', 'string', 'in:bailleur,agence'],
            'statut' => ['required', 'string', 'in:brouillon,en_attente_signature,actif,suspendu,expire,resilie,archive'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
