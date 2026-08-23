<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Rôles autorisés par capacité (ability) du module Locative.
     */
    protected array $matriceRoles = [
        'locative.gerer' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER],
        'locative.finances' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE],
        'locative.operations-sensibles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE],
        'locative.corbeille' => [Role::ADMINISTRATEUR, Role::DIRECTEUR],
        'locative.suppression-definitive' => [Role::ADMINISTRATEUR],
        'locative.journal' => [Role::ADMINISTRATEUR, Role::DIRECTEUR],
        'locative.documents' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT],

        'commercial.gerer' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT],
        'commercial.operations-sensibles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR],
    ];

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
        foreach ($this->matriceRoles as $ability => $rolesAutorises) {
            Gate::define($ability, fn ($user) => $user->aLeRole(...$rolesAutorises));
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
