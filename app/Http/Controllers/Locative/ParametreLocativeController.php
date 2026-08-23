<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\CategorieBien;
use App\Models\ModePaiement;

class ParametreLocativeController extends Controller
{
    public function index()
    {
        $categories = CategorieBien::orderBy('nom')->get();
        $modesPaiement = ModePaiement::orderBy('nom')->get();

        return view('locative.parametres', compact('categories', 'modesPaiement'));
    }
}
