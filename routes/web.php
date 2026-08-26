<?php

use App\Http\Controllers\Central\AdminAuthController;
use App\Http\Controllers\Central\CentralDashboardController;
use App\Http\Controllers\Central\PackageController;
use App\Http\Controllers\Central\ReglageController as CentralReglageController;
use App\Http\Controllers\Central\SocieteController;
use Illuminate\Support\Facades\Route;

// Domaine central (takha.test, localhost, 127.0.0.1) : espace "Administrateur du logiciel" —
// gestion des sociétés (PME), des packages et des licences. Tout est volontairement préfixé
// /admin pour ne jamais partager un chemin identique avec routes/tenant.php (une route GET / ou
// GET /connexion strictement identique dans les deux fichiers se ferait sinon écraser
// silencieusement par la dernière enregistrée — bug de collision de RouteCollection).
Route::get('/', fn () => redirect()->route('central.login'));

Route::get('admin/connexion', [AdminAuthController::class, 'showLogin'])->name('central.login');
Route::post('admin/connexion', [AdminAuthController::class, 'login'])->name('central.login.store');
Route::post('admin/deconnexion', [AdminAuthController::class, 'logout'])->name('central.logout');

Route::prefix('admin')->name('central.')->middleware('auth:admin')->group(function () {
    Route::get('/', fn () => redirect()->route('central.dashboard'));

    Route::get('tableau-de-bord', [CentralDashboardController::class, 'index'])->name('dashboard');

    Route::get('societes', [SocieteController::class, 'index'])->name('societes.index');
    Route::get('societes/creer', [SocieteController::class, 'create'])->name('societes.create');
    Route::post('societes', [SocieteController::class, 'store'])->name('societes.store');
    Route::get('societes/{tenant}', [SocieteController::class, 'show'])->name('societes.show');
    Route::post('societes/{tenant}/statut', [SocieteController::class, 'toggleStatut'])->name('societes.toggle-statut');
    Route::post('societes/{tenant}/modules/{cle}', [SocieteController::class, 'toggleModule'])->name('societes.toggle-module');
    Route::post('societes/{tenant}/licence', [SocieteController::class, 'genererLicence'])->name('societes.generer-licence');

    Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
    Route::post('packages', [PackageController::class, 'store'])->name('packages.store');
    Route::put('packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::post('packages/{package}/toggle', [PackageController::class, 'toggle'])->name('packages.toggle');

    Route::get('reglages', [CentralReglageController::class, 'index'])->name('reglages.index');
    Route::put('reglages/general', [CentralReglageController::class, 'updateGeneral'])->name('reglages.update-general');
    Route::put('reglages/smtp', [CentralReglageController::class, 'updateSmtp'])->name('reglages.update-smtp');
    Route::put('reglages/integrations', [CentralReglageController::class, 'updateIntegrations'])->name('reglages.update-integrations');
});
