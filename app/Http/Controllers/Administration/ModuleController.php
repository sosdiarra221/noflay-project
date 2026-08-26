<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Support\Facades\Gate;

class ModuleController extends Controller
{
    /**
     * Aperçu en lecture seule des modules souscrits par la société. L'activation/désactivation
     * n'est plus faite ici : elle appartient exclusivement à l'espace central "Administrateur
     * du logiciel", selon l'abonnement de la PME (voir Module::estActif()).
     */
    public function index()
    {
        Gate::authorize('administration.gerer');

        $modules = Module::orderBy('ordre')->get()
            ->map(fn (Module $module) => (object) [
                'cle' => $module->cle,
                'nom' => $module->nom,
                'description' => $module->description,
                'icone' => $module->icone,
                'actif' => Module::estActif($module->cle),
            ]);

        return view('administration.modules.index', compact('modules'));
    }
}
