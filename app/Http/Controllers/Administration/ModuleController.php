<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::orderBy('ordre')->get();

        return view('administration.modules.index', compact('modules'));
    }

    public function toggle(Request $request, Module $module)
    {
        Gate::authorize('administration.gerer');

        $module->update(['actif' => ! $module->actif]);

        return back()->with('success', $module->nom.' a été '.($module->actif ? 'activé' : 'désactivé').'.');
    }
}
