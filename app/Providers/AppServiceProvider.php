<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Les permissions (abilities Gate) sont désormais gérées dynamiquement depuis la
        // table `permissions` / `permission_role` (module Direction & Administration >
        // Rôles & permissions), au lieu d'une matrice codée en dur. La vérification
        // Schema::hasTable protège les commandes exécutées avant que les migrations
        // n'aient tourné (ex. premier `php artisan migrate` sur une base vierge).
        if (Schema::hasTable('permissions') && Schema::hasTable('permission_role')) {
            foreach (Permission::with('roles')->get() as $permission) {
                $rolesAutorises = $permission->roles->pluck('nom')->all();

                Gate::define($permission->cle, fn ($user) => $user->aLeRole(...$rolesAutorises));
            }
        }

        // Un administrateur passe outre toutes les vérifications.
        Gate::before(fn ($user, $ability) => $user->aLeRole(Role::ADMINISTRATEUR) ? true : null);

        // Date longue en français, ex: "Lundi 01 Septembre 2026".
        Carbon::macro('versionLongue', function () {
            /** @var \Illuminate\Support\Carbon $this */
            return ucfirst($this->translatedFormat('l d F Y'));
        });
    }
}
