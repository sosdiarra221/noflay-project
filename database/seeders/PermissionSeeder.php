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

            // Permissions granulaires par action — Absences (RH), Prospects (Commercial),
            // Dépenses (Finance), Devis/Factures (Facturation), Contrats (Locative).
            // "voir-siens" limite la liste aux éléments dont l'utilisateur est propriétaire
            // (voir AbsenceController/ProspectController/DepenseController/DevisController/
            // FactureController) ; "voir-tout" prévaut si le rôle possède les deux.
            ['cle' => 'rh.absences.voir-tout', 'libelle' => 'Absences : voir toutes les demandes', 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'rh.absences.voir-siens', 'libelle' => 'Absences : voir ses propres demandes', 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::ASSISTANT]],
            ['cle' => 'rh.absences.ajouter', 'libelle' => 'Absences : créer une demande', 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::ASSISTANT]],
            ['cle' => 'rh.absences.modifier', 'libelle' => 'Absences : modifier une demande', 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'rh.absences.supprimer', 'libelle' => 'Absences : supprimer une demande', 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR]],
            ['cle' => 'rh.absences.statut', 'libelle' => "Absences : valider/refuser une demande", 'module' => 'RH', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],

            ['cle' => 'commercial.prospects.voir-tout', 'libelle' => 'Prospects : voir tous les prospects', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'commercial.prospects.voir-siens', 'libelle' => 'Prospects : voir ses propres prospects', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'commercial.prospects.ajouter', 'libelle' => 'Prospects : ajouter un prospect', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'commercial.prospects.modifier', 'libelle' => 'Prospects : modifier un prospect', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'commercial.prospects.supprimer', 'libelle' => 'Prospects : supprimer un prospect', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'commercial.prospects.statut', 'libelle' => 'Prospects : changer le statut', 'module' => 'Commercial', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],

            ['cle' => 'finance.depenses.voir-tout', 'libelle' => 'Dépenses : voir toutes les dépenses', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'finance.depenses.voir-siens', 'libelle' => 'Dépenses : voir ses propres dépenses', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'finance.depenses.ajouter', 'libelle' => 'Dépenses : créer une dépense', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'finance.depenses.modifier', 'libelle' => 'Dépenses : modifier une dépense', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'finance.depenses.supprimer', 'libelle' => 'Dépenses : supprimer une dépense', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR]],
            ['cle' => 'finance.depenses.statut', 'libelle' => 'Dépenses : valider/approuver/payer', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'finance.comptabilite', 'libelle' => 'Comptabilité générale : rapport consolidé et solde global', 'module' => 'Finance', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],

            ['cle' => 'facturation.devis.voir-tout', 'libelle' => 'Devis : voir tous les devis', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'facturation.devis.voir-siens', 'libelle' => 'Devis : voir ses propres devis', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'facturation.devis.ajouter', 'libelle' => 'Devis : créer un devis', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'facturation.devis.modifier', 'libelle' => 'Devis : modifier un devis', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'facturation.devis.supprimer', 'libelle' => 'Devis : supprimer un devis', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'facturation.devis.statut', 'libelle' => 'Devis : changer le statut (gagné/perdu...)', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'facturation.factures.voir-tout', 'libelle' => 'Factures : voir toutes les factures', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'facturation.factures.voir-siens', 'libelle' => 'Factures : voir ses propres factures', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER, Role::ASSISTANT]],
            ['cle' => 'facturation.factures.modifier', 'libelle' => 'Factures : modifier une facture', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],
            ['cle' => 'facturation.factures.statut', 'libelle' => 'Factures : marquer payée/annulée', 'module' => 'Facturation', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::COMPTABLE]],

            ['cle' => 'locative.contrats.ajouter', 'libelle' => 'Contrats de location : créer', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER]],
            ['cle' => 'locative.contrats.modifier', 'libelle' => 'Contrats de location : modifier', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER]],
            ['cle' => 'locative.contrats.supprimer', 'libelle' => 'Contrats de location : supprimer', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR]],
            ['cle' => 'locative.contrats.statut', 'libelle' => 'Contrats de location : suspendre/résilier/renouveler', 'module' => 'Locative', 'roles' => [Role::ADMINISTRATEUR, Role::DIRECTEUR, Role::AGENT_IMMOBILIER]],
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
