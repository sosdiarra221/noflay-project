<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SocieteController extends Controller
{
    public function index()
    {
        $societes = Tenant::with('domains')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Tenant $tenant) {
                $tenant->nb_modules_actifs = DB::connection('central')
                    ->table('tenant_modules')
                    ->where('tenant_id', $tenant->id)
                    ->where('actif', true)
                    ->count();

                return $tenant;
            });

        return view('central.societes.index', compact('societes'));
    }

    public function create()
    {
        return view('central.societes.create', ['catalogue' => config('modules')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'alpha_dash', 'max:50', 'unique:central.tenants,id'],
            'nom_pme' => ['required', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'max:100'],
            'modules' => ['array'],
            'modules.*' => ['string', Rule::in(array_keys(config('modules')))],
        ]);

        $tenant = Tenant::create([
            'id' => $data['id'],
            'nom_pme' => $data['nom_pme'],
            'statut' => 'actif',
            'plan' => $data['plan'] ?? null,
        ]);

        $domaine = $tenant->domains()->create([
            'domain' => $data['id'].'.'.config('app.tenant_domain_suffix'),
        ]);

        $modulesAccordes = $data['modules'] ?? [];

        foreach (config('modules') as $cle => $meta) {
            if ($cle === 'administration') {
                continue;
            }

            DB::connection('central')->table('tenant_modules')->updateOrInsert(
                ['tenant_id' => $tenant->id, 'module_cle' => $cle],
                ['actif' => in_array($cle, $modulesAccordes, true), 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return redirect()->route('central.societes.show', $tenant)
            ->with('success', "Société « {$tenant->nom_pme} » créée. Sa base de données a été provisionnée et son sous-domaine est {$domaine->domain}.");
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');

        $modulesAccordes = DB::connection('central')
            ->table('tenant_modules')
            ->where('tenant_id', $tenant->id)
            ->pluck('actif', 'module_cle');

        $modules = collect(config('modules'))->map(fn ($meta, $cle) => (object) [
            'cle' => $cle,
            'nom' => $meta['nom'],
            'description' => $meta['description'],
            'icone' => $meta['icone'],
            'actif' => $cle === 'administration' ? true : (bool) ($modulesAccordes[$cle] ?? false),
        ])->values();

        return view('central.societes.show', compact('tenant', 'modules'));
    }

    public function toggleStatut(Tenant $tenant)
    {
        $tenant->update(['statut' => $tenant->statut === 'actif' ? 'suspendu' : 'actif']);

        return back()->with('success', "Compte {$tenant->nom_pme} ".($tenant->statut === 'actif' ? 'réactivé' : 'suspendu').'.');
    }

    public function toggleModule(Request $request, Tenant $tenant, string $cle)
    {
        abort_if($cle === 'administration', 403, "Ce module est toujours actif et n'est pas modifiable.");
        abort_unless(array_key_exists($cle, config('modules')), 404);

        $ligne = DB::connection('central')->table('tenant_modules')
            ->where('tenant_id', $tenant->id)->where('module_cle', $cle)->first();

        DB::connection('central')->table('tenant_modules')->updateOrInsert(
            ['tenant_id' => $tenant->id, 'module_cle' => $cle],
            ['actif' => ! ($ligne->actif ?? false), 'updated_at' => now(), 'created_at' => now()]
        );

        return back()->with('success', 'Module mis à jour.');
    }
}
