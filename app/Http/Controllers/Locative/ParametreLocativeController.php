<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\CategorieBien;
use App\Models\ModePaiement;
use App\Models\Reglage;
use Illuminate\Http\Request;

class ParametreLocativeController extends Controller
{
    public function index()
    {
        $categories = CategorieBien::orderBy('nom')->get();
        $modesPaiement = ModePaiement::orderBy('nom')->get();
        $reglage = Reglage::courant();

        return view('locative.parametres', compact('categories', 'modesPaiement', 'reglage'));
    }

    public function updateTaxes(Request $request)
    {
        $data = $request->validate([
            'taux_tva_defaut' => ['required', 'numeric', 'min:0', 'max:100'],
            'taux_tom_defaut' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        Reglage::courant()->update($data);

        return back()->with('success', 'Taux par défaut mis à jour avec succès.');
    }
}
