<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\Bien;
use App\Models\CategorieBien;
use Illuminate\Http\Request;

class BienDisponibleController extends Controller
{
    public function index(Request $request)
    {
        $biens = Bien::with(['bailleur', 'categorie'])
            ->whereIn('statut', ['disponible', 'a_vendre'])
            ->when($request->filled('categorie_bien_id'), fn ($q) => $q->where('categorie_bien_id', $request->categorie_bien_id))
            ->when($request->filled('type_exploitation'), fn ($q) => $q->where('type_exploitation', $request->type_exploitation))
            ->when($request->filled('bailleur_id'), fn ($q) => $q->where('bailleur_id', $request->bailleur_id))
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $recherche = $request->recherche;
                $q->where(function ($sous) use ($recherche) {
                    $sous->where('titre', 'like', "%{$recherche}%")
                        ->orWhere('adresse', 'like', "%{$recherche}%")
                        ->orWhere('zone', 'like', "%{$recherche}%");
                });
            })
            ->latest()
            ->get();

        $bailleurs = Bailleur::orderBy('nom')->get();
        $categories = CategorieBien::orderBy('nom')->get();

        return view('commercial.biens-disponibles', compact('biens', 'bailleurs', 'categories'));
    }
}
