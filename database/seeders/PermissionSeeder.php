<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Reprend exactement la matrice de rôles historiquement codée en dur dans
     * AppServiceProvider, pour que le passage au système dynamique (table permissions +
     * permission_role, gérable depuis Direction & Administration) ne change aucun
     * comportement existant tant qu'un administrateur ne modifie rien depuis l'écran Rôles.
     */
    public function run(): void
    {
        $permissions = [
            ['cle' => 'locative.gerer', 'libelle' => 'Gérer le module Locative', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER]],
            ['cle' => 'locative.finances', 'libelle' => 'Consulter les finances locatives', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'locative.operations-sensibles', 'libelle' => 'Opérations sensibles (locative)', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'locative.corbeille', 'libelle' => 'Accéder à la corbeille', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'locative.suppression-definitive', 'libelle' => 'Supprimer définitivement', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR]],
            ['cle' => 'locative.journal', 'libelle' => "Consulter le journal d'activité", 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'locative.documents', 'libelle' => 'Gérer les documents (locative)', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],

            ['cle' => 'commercial.gerer', 'libelle' => 'Gérer le module Commercial', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'commercial.operations-sensibles', 'libelle' => 'Opérations sensibles (commercial)', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],

            ['cle' => 'documents.gerer', 'libelle' => 'Gérer le module Gestion Document', 'module' => 'Documents', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'documents.templates', 'libelle' => 'Gérer les modèles de documents', 'module' => 'Documents', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],

            ['cle' => 'finance.consulter', 'libelle' => 'Consulter le module Finance', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'finance.gerer', 'libelle' => 'Gérer le module Finance', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],

            ['cle' => 'administration.gerer', 'libelle' => 'Gérer Direction & Administration', 'module' => 'Administration', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],

            ['cle' => 'rh.consulter', 'libelle' => "Consulter l'annuaire RH", 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::ASSISTANT]],
            ['cle' => 'rh.gerer', 'libelle' => 'Gérer le module RH (employés, contrats)', 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::ASSISTANT]],
            ['cle' => 'rh.donnees-sensibles', 'libelle' => 'Consulter les données sensibles (identité, RIB, famille)', 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
        ];

        foreach ($permissions as $definition) {
            $permission = Permission::updateOrCreate(
                ['cle' => $definition['cle']],
                ['libelle' => $definition['libelle'], 'module' => $definition['module']]
            );

            $roleIds = Role::whereIn('nom', $definition['roles'])->pluck('id');
            $permission->roles()->syncWithoutDetaching($roleIds);
        }
    }
}
