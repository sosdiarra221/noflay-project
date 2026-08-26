<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\CategorieBien;
use App\Models\CategorieDepense;
use App\Models\Locative\ParametreLocative;
use App\Models\ModePaiement;
use App\Models\Reglage;
use Illuminate\Http\Request;

class ParametreLocativeController extends Controller
{
    public function index()
    {
        $categories = CategorieBien::orderBy('nom')->get();
        $modesPaiement = ModePaiement::orderBy('nom')->get();
        $categoriesDepense = CategorieDepense::orderBy('nom')->get();
        $reglage = Reglage::courant();
        $agence = ParametreLocative::courant();

        return view('locative.parametres', compact('categories', 'modesPaiement', 'categoriesDepense', 'reglage', 'agence'));
    }

    public function updateTaxes(Request $request)
    {
        $data = $request->validate([
            'taux_tva_defaut' => ['required', 'numeric', 'min:0', 'max:100'],
            'taux_tom_defaut' => ['required', 'numeric', 'min:0', 'max:100'],
            'commission_regime' => ['required', 'in:HT,TTC'],
        ]);

        Reglage::courant()->update($data);

        return back()->with('success', 'Taux par défaut mis à jour avec succès.');
    }

    public function updateAgence(Request $request)
    {
        $data = $request->validate([
            'nom_societe' => ['required', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'site_web' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $dossier = public_path('uploads/logos');
            if (! is_dir($dossier)) {
                mkdir($dossier, 0755, true);
            }

            $fichier = $request->file('logo');
            $nomFichier = uniqid('logo_').'.'.$fichier->getClientOriginalExtension();
            $fichier->move($dossier, $nomFichier);

            $data['logo'] = 'uploads/logos/'.$nomFichier;
        }

        ParametreLocative::courant()->update($data);

        return back()->with('success', "Les informations de l'agence (module Locative) ont été mises à jour.");
    }
}
