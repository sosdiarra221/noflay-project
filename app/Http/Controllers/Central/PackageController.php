<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('licences')->orderBy('nom')->get();

        return view('central.packages.index', [
            'packages' => $packages,
            'catalogue' => config('modules'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        Package::create($data);

        return back()->with('success', "Package « {$data['nom']} » créé.");
    }

    public function update(Request $request, Package $package)
    {
        $data = $this->valider($request);

        $package->update($data);

        return back()->with('success', "Package « {$data['nom']} » mis à jour.");
    }

    public function toggle(Package $package)
    {
        $package->update(['actif' => ! $package->actif]);

        return back()->with('success', 'Package '.($package->actif ? 'activé' : 'désactivé').'.');
    }

    protected function valider(Request $request): array
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'modules' => ['array'],
            'modules.*' => ['string', Rule::in(array_keys(config('modules')))],
        ]);

        $data['modules'] = array_values(array_diff($data['modules'] ?? [], ['administration']));

        return $data;
    }
}
