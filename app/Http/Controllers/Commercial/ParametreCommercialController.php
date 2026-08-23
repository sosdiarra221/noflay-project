<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commercial\Source;
use App\Models\Commercial\TypeDemande;

class ParametreCommercialController extends Controller
{
    public function index()
    {
        $sources = Source::orderBy('nom')->get();
        $typesDemande = TypeDemande::orderBy('nom')->get();

        return view('commercial.parametres', compact('sources', 'typesDemande'));
    }
}
