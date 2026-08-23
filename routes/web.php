<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\DeviseController;
use App\Http\Controllers\ReglageController;
use App\Http\Controllers\Locative\LocativeDashboardController;
use App\Http\Controllers\Locative\ParametreLocativeController;
use App\Http\Controllers\Locative\CategorieBienController;
use App\Http\Controllers\Locative\ModePaiementController;
use App\Http\Controllers\Locative\BailleurController;
use App\Http\Controllers\Locative\ContratGeranceController;
use App\Http\Controllers\Locative\BienController;
use App\Http\Controllers\Locative\LocataireController;
use App\Http\Controllers\Locative\LocationController;
use App\Http\Controllers\Locative\ContratLocationController;
use App\Http\Controllers\Locative\EcheanceLoyerController;
use App\Http\Controllers\Locative\PaiementController;
use App\Http\Controllers\Locative\ReversementController;

Route::get('/', function () {
    return view('index');
});

Route::get('reglages', [ReglageController::class, 'index'])->name('reglages.index');
Route::put('reglages/general', [ReglageController::class, 'updateGeneral'])->name('reglages.update-general');
Route::put('reglages/smtp', [ReglageController::class, 'updateSmtp'])->name('reglages.update-smtp');

Route::post('devises', [DeviseController::class, 'store'])->name('devises.store');
Route::put('devises/{devise}', [DeviseController::class, 'update'])->name('devises.update');
Route::delete('devises/{devise}', [DeviseController::class, 'destroy'])->name('devises.destroy');

Route::get('departements', [DepartementController::class, 'index'])->name('departements.index');
Route::post('departements', [DepartementController::class, 'store'])->name('departements.store');
Route::put('departements/{departement}', [DepartementController::class, 'update'])->name('departements.update');
Route::delete('departements/{departement}', [DepartementController::class, 'destroy'])->name('departements.destroy');

// Module Locative — sous-application avec son propre dashboard et son propre menu.
Route::prefix('locative')->name('locative.')->group(function () {
    Route::get('/', [LocativeDashboardController::class, 'index'])->name('dashboard');

    Route::get('parametres', [ParametreLocativeController::class, 'index'])->name('parametres.index');
    Route::post('categories-biens', [CategorieBienController::class, 'store'])->name('categories-biens.store');
    Route::put('categories-biens/{categorie}', [CategorieBienController::class, 'update'])->name('categories-biens.update');
    Route::delete('categories-biens/{categorie}', [CategorieBienController::class, 'destroy'])->name('categories-biens.destroy');
    Route::post('modes-paiement', [ModePaiementController::class, 'store'])->name('modes-paiement.store');
    Route::put('modes-paiement/{mode}', [ModePaiementController::class, 'update'])->name('modes-paiement.update');
    Route::delete('modes-paiement/{mode}', [ModePaiementController::class, 'destroy'])->name('modes-paiement.destroy');

    Route::get('bailleurs', [BailleurController::class, 'index'])->name('bailleurs.index');
    Route::get('bailleurs/{bailleur}', [BailleurController::class, 'show'])->name('bailleurs.show');
    Route::post('bailleurs', [BailleurController::class, 'store'])->name('bailleurs.store');
    Route::put('bailleurs/{bailleur}', [BailleurController::class, 'update'])->name('bailleurs.update');
    Route::delete('bailleurs/{bailleur}', [BailleurController::class, 'destroy'])->name('bailleurs.destroy');

    Route::get('gerances', [ContratGeranceController::class, 'index'])->name('gerances.index');
    Route::get('gerances/creer', [ContratGeranceController::class, 'create'])->name('gerances.create');
    Route::get('gerances/{gerance}', [ContratGeranceController::class, 'show'])->name('gerances.show');
    Route::get('gerances/{gerance}/pdf', [ContratGeranceController::class, 'pdf'])->name('gerances.pdf');
    Route::post('gerances', [ContratGeranceController::class, 'store'])->name('gerances.store');
    Route::put('gerances/{gerance}', [ContratGeranceController::class, 'update'])->name('gerances.update');
    Route::delete('gerances/{gerance}', [ContratGeranceController::class, 'destroy'])->name('gerances.destroy');

    Route::get('biens', [BienController::class, 'index'])->name('biens.index');
    Route::post('biens', [BienController::class, 'store'])->name('biens.store');
    Route::put('biens/{bien}', [BienController::class, 'update'])->name('biens.update');
    Route::delete('biens/{bien}', [BienController::class, 'destroy'])->name('biens.destroy');

    Route::get('locataires', [LocataireController::class, 'index'])->name('locataires.index');
    Route::post('locataires', [LocataireController::class, 'store'])->name('locataires.store');
    Route::put('locataires/{locataire}', [LocataireController::class, 'update'])->name('locataires.update');
    Route::delete('locataires/{locataire}', [LocataireController::class, 'destroy'])->name('locataires.destroy');

    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('locations/creer', [LocationController::class, 'create'])->name('locations.create');
    Route::get('locations/{location}', [LocationController::class, 'show'])->name('locations.show');
    Route::post('locations', [LocationController::class, 'store'])->name('locations.store');

    Route::get('contrats/{contrat}', [ContratLocationController::class, 'show'])->name('contrats.show');
    Route::put('contrats/{contrat}', [ContratLocationController::class, 'update'])->name('contrats.update');
    Route::delete('contrats/{contrat}', [ContratLocationController::class, 'destroy'])->name('contrats.destroy');
    Route::get('contrats/{contrat}/pdf', [ContratLocationController::class, 'pdf'])->name('contrats.pdf');
    Route::post('contrats/{contrat}/generer-loyers', [ContratLocationController::class, 'genererLoyers'])->name('contrats.generer-loyers');

    Route::get('echeances', [EcheanceLoyerController::class, 'index'])->name('echeances.index');
    Route::post('echeances/{echeance}/encaisser', [EcheanceLoyerController::class, 'encaisser'])->name('echeances.encaisser');

    Route::get('paiements/{paiement}/recu', [PaiementController::class, 'pdf'])->name('paiements.recu');

    Route::get('reversements', [ReversementController::class, 'index'])->name('reversements.index');
});

Route::get('{any}', [DashboardController::class, 'index'])->where('any', '.*'); // Catch-all route for the dashboard.
