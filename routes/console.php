<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Détecte quotidiennement les loyers en retard, échéances proches et contrats bientôt
// expirés, et déclenche les notifications correspondantes. Nécessite que le planificateur
// tourne réellement (`php artisan schedule:work` en développement, ou une tâche planifiée
// Windows/cron appelant `php artisan schedule:run` chaque minute en production).
Schedule::command('notifications:verifier-evenements-planifies')->dailyAt('07:00');

// Crédite chaque employé actif sous contrat CDI/CDD de 2 jours de congé, le 1er de chaque mois.
Schedule::command('rh:incrementer-soldes-conges')->monthlyOn(1, '00:05');
