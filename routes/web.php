<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\DeviseController;
use App\Http\Controllers\ReglageController;

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

Route::get('{any}', [DashboardController::class, 'index'])->where('any', '.*'); // Catch-all route for the dashboard.
