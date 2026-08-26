<?php

// Catalogue unique des modules de l'application — source de vérité partagée entre le seeder
// tenant (ModuleSeeder, affichage local) et l'espace central (cases à cocher lors de la
// création/gestion d'une PME). "administration" est toujours actif pour chaque PME et n'est
// jamais proposé comme case à cocher côté central (voir Module::estActif()).
return [
    'locative' => ['nom' => 'Module Locative', 'description' => 'Gestion des bailleurs, biens, locataires, contrats de location, loyers, cautions et reversements.', 'icone' => 'bi-building', 'ordre' => 1],
    'finance' => ['nom' => 'Module Finance', 'description' => 'Suivi financier global : dépenses, cautions, reversements aux bailleurs, journal de caisse.', 'icone' => 'bi-cash-coin', 'ordre' => 2],
    'commercial' => ['nom' => 'Module Commercial', 'description' => 'Prospection, suivi des demandes clients, partenaires et conversion en location.', 'icone' => 'bi-bullseye', 'ordre' => 3],
    'documents' => ['nom' => 'Gestion Document', 'description' => 'Modèles de contrats et documents générés automatiquement pour les autres modules.', 'icone' => 'bi-file-earmark-text', 'ordre' => 4],
    'facturation' => ['nom' => 'Module Facturation', 'description' => 'Création de devis, gestion des clients/prospects et suivi de la facturation.', 'icone' => 'bi-receipt', 'ordre' => 5],
    'rh' => ['nom' => 'Module RH', 'description' => 'Fiches employés, situation familiale, contrats de travail et affectations.', 'icone' => 'bi-person-vcard', 'ordre' => 6],
    'administration' => ['nom' => 'Direction & Administration', 'description' => 'Utilisateurs, rôles et permissions, sécurité, réglages généraux de la société.', 'icone' => 'bi-shield-lock', 'ordre' => 7],
];
