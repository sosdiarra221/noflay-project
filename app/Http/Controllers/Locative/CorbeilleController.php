<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\Bien;
use App\Models\ContratGerance;
use App\Models\ContratLocation;
use App\Models\Locataire;
use Illuminate\Support\Facades\Gate;

class CorbeilleController extends Controller
{
    protected array $modeles = [
        'bailleur' => ['classe' => Bailleur::class, 'libelle' => 'Bailleur', 'route' => 'bailleurs'],
        'gerance' => ['classe' => ContratGerance::class, 'libelle' => 'Contrat de gérance', 'route' => 'gerances'],
        'bien' => ['classe' => Bien::class, 'libelle' => 'Bien', 'route' => 'biens'],
        'locataire' => ['classe' => Locataire::class, 'libelle' => 'Locataire', 'route' => 'locataires'],
        'contrat_location' => ['classe' => ContratLocation::class, 'libelle' => 'Contrat de location', 'route' => 'contrats'],
    ];

    public function index()
    {
        Gate::authorize('locative.corbeille');

        $elements = collect();

        foreach ($this->modeles as $cle => $info) {
            $elements = $elements->merge(
                $info['classe']::onlyTrashed()->with('supprimePar')->get()->map(function ($item) use ($cle, $info) {
                    return (object) [
                        'type' => $cle,
                        'libelle_type' => $info['libelle'],
                        'id' => $item->id,
                        'libelle' => $this->libellePour($cle, $item),
                        'deleted_at' => $item->deleted_at,
                        'motif_suppression' => $item->motif_suppression,
                        'supprime_par' => $item->supprimePar->name ?? null,
                    ];
                })
            );
        }

        $elements = $elements->sortByDesc('deleted_at')->values();

        return view('locative.corbeille.index', compact('elements'));
    }

    public function restaurer(string $type, int $id)
    {
        Gate::authorize('locative.corbeille');

        $classe = $this->modeles[$type]['classe'] ?? abort(404);

        $classe::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Élément restauré avec succès.');
    }

    public function supprimerDefinitivement(string $type, int $id)
    {
        Gate::authorize('locative.suppression-definitive');

        $classe = $this->modeles[$type]['classe'] ?? abort(404);

        $classe::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'Élément supprimé définitivement.');
    }

    protected function libellePour(string $type, $item): string
    {
        return match ($type) {
            'bailleur' => $item->nom_complet,
            'gerance' => $item->numero,
            'bien' => $item->titre,
            'locataire' => $item->nom_complet,
            'contrat_location' => $item->numero,
            default => (string) $item->id,
        };
    }
}
