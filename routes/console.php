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
