<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use App\Models\Licence;
use App\Models\Package;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

                $tenant->derniere_licence = Licence::where('tenant_id', $tenant->id)->orderByDesc('date_fin')->first();

                return $tenant;
            });

        return view('central.societes.index', compact('societes'));
    }

    public function create()
    {
        $packages = Package::where('actif', true)->orderBy('nom')->get();

        return view('central.societes.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'alpha_dash', 'max:50', 'unique:central.tenants,id'],
            'nom_pme' => ['required', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'max:100'],
            'utilisateur_nom' => ['required', 'string', 'max:255'],
            'utilisateur_email' => ['required', 'email', 'max:255'],
            'utilisateur_password' => ['required', 'string', 'min:8'],
            'package_id' => ['required', 'exists:central.packages,id'],
            'duree_jours' => ['required', 'integer', 'min:1'],
        ]);

        $package = Package::findOrFail($data['package_id']);

        $tenant = Tenant::create([
            'id' => $data['id'],
            'nom_pme' => $data['nom_pme'],
            'statut' => 'actif',
            'plan' => $data['plan'] ?? $package->nom,
        ]);

        $domaine = $tenant->domains()->create([
            'domain' => $data['id'].'.'.config('app.tenant_domain_suffix'),
        ]);

        // Provisionnement complet : la base est créée + migrée + seedée (données de référence,
        // voir TenantSeeder) de manière synchrone par les events du TenancyServiceProvider avant
        // qu'on arrive ici. On peut donc créer le premier utilisateur directement dedans.
        $tenant->run(function () use ($data) {
            User::create([
                'name' => $data['utilisateur_nom'],
                'email' => $data['utilisateur_email'],
                'password' => Hash::make($data['utilisateur_password']),
                'role_id' => Role::where('nom', Role::ADMINISTRATEUR)->value('id'),
                'departement_id' => Departement::where('nom', 'Direction')->value('id'),
                'statut' => 'actif',
            ]);
        });

        $this->genererLicencePourTenant($tenant, $package, (int) $data['duree_jours']);

        return redirect()->route('central.societes.show', $tenant)->with('success',
            "Société « {$tenant->nom_pme} » créée sur {$domaine->domain}. ".
            "Accès : {$data['utilisateur_email']} / mot de passe transmis à la création."
        );
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

        $licences = Licence::where('tenant_id', $tenant->id)->with('package', 'genereParAdmin')->orderByDesc('date_fin')->get();
        $licenceActuelle = $licences->first();
        $packages = Package::where('actif', true)->orderBy('nom')->get();

        return view('central.societes.show', compact('tenant', 'modules', 'licences', 'licenceActuelle', 'packages'));
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

    /**
     * Génère une nouvelle licence (= "réinitialiser les accès") : réactive le compte et
     * remplace les modules accordés par ceux du package choisi.
     */
    public function genererLicence(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'package_id' => ['required', 'exists:central.packages,id'],
            'duree_jours' => ['required', 'integer', 'min:1'],
        ]);

        $package = Package::findOrFail($data['package_id']);

        $this->genererLicencePourTenant($tenant, $package, (int) $data['duree_jours']);

        return back()->with('success', "Nouvelle licence générée pour {$tenant->nom_pme} — accès valide jusqu'au ".now()->addDays((int) $data['duree_jours'])->format('d/m/Y').'.');
    }

    protected function genererLicencePourTenant(Tenant $tenant, Package $package, int $dureeJours): Licence
    {
        $tenant->update(['statut' => 'actif']);

        foreach (config('modules') as $cle => $meta) {
            if ($cle === 'administration') {
                continue;
            }

            DB::connection('central')->table('tenant_modules')->updateOrInsert(
                ['tenant_id' => $tenant->id, 'module_cle' => $cle],
                ['actif' => in_array($cle, $package->modules, true), 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return Licence::create([
            'tenant_id' => $tenant->id,
            'package_id' => $package->id,
            'date_debut' => Carbon::today(),
            'date_fin' => Carbon::today()->addDays($dureeJours),
            'genere_par_admin_id' => Auth::guard('admin')->id(),
        ]);
    }
}
