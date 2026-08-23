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
use App\Http\Controllers\Locative\CorbeilleController;
use App\Http\Controllers\Locative\JournalActiviteController;
use App\Http\Controllers\Locative\DocumentController;
use App\Http\Controllers\Locative\EncaissementController;
use App\Http\Controllers\Locative\FicheLocativeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Commercial\CommercialDashboardController;
use App\Http\Controllers\Commercial\ProspectController;
use App\Http\Controllers\Commercial\ActiviteController;
use App\Http\Controllers\Commercial\ParametreCommercialController;
use App\Http\Controllers\Commercial\SourceController;
use App\Http\Controllers\Commercial\TypeDemandeController;
use App\Http\Controllers\Commercial\AgendaController;
use App\Http\Controllers\Commercial\PartenaireController;
use App\Http\Controllers\Commercial\RapportController;
use App\Http\Controllers\Commercial\BienDisponibleController;

Route::get('connexion', [AuthController::class, 'showLogin'])->name('login');
Route::post('connexion', [AuthController::class, 'login'])->name('login.store');

Route::middleware('auth')->group(function () {

Route::post('deconnexion', [AuthController::class, 'logout'])->name('logout');

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
    Route::get('locataires/{locataire}', [LocataireController::class, 'show'])->name('locataires.show');
    Route::post('locataires', [LocataireController::class, 'store'])->name('locataires.store');
    Route::put('locataires/{locataire}', [LocataireController::class, 'update'])->name('locataires.update');
    Route::delete('locataires/{locataire}', [LocataireController::class, 'destroy'])->name('locataires.destroy');

    Route::post('fiches-locatives', [FicheLocativeController::class, 'store'])->name('fiches-locatives.store');
    Route::get('fiches-locatives/{fiche}/apercu', [FicheLocativeController::class, 'apercu'])->name('fiches-locatives.apercu');
    Route::get('fiches-locatives/{fiche}/telecharger', [FicheLocativeController::class, 'telecharger'])->name('fiches-locatives.telecharger');

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
    Route::post('echeances/generer', [EcheanceLoyerController::class, 'generer'])->name('echeances.generer');
    Route::post('echeances/{echeance}/encaisser', [EcheanceLoyerController::class, 'encaisser'])->name('echeances.encaisser');
    Route::get('echeances/{echeance}/quittance', [EcheanceLoyerController::class, 'apercuQuittance'])->name('echeances.quittance');
    Route::get('echeances/{echeance}/quittance/telecharger', [EcheanceLoyerController::class, 'telechargerQuittance'])->name('echeances.quittance-telecharger');

    Route::get('encaissements', [EncaissementController::class, 'index'])->name('encaissements.index');

    Route::get('paiements/{paiement}/recu', [PaiementController::class, 'pdf'])->name('paiements.recu');
    Route::get('paiements/{paiement}/apercu', [PaiementController::class, 'apercu'])->name('paiements.apercu');
    Route::post('paiements/{paiement}/annuler', [PaiementController::class, 'annuler'])->name('paiements.annuler');

    Route::get('reversements', [ReversementController::class, 'index'])->name('reversements.index');

    Route::get('corbeille', [CorbeilleController::class, 'index'])->name('corbeille.index');
    Route::post('corbeille/{type}/{id}/restaurer', [CorbeilleController::class, 'restaurer'])->name('corbeille.restaurer');
    Route::delete('corbeille/{type}/{id}', [CorbeilleController::class, 'supprimerDefinitivement'])->name('corbeille.supprimer-definitivement');

    Route::get('journal', [JournalActiviteController::class, 'index'])->name('journal.index');

    Route::post('documents/{type}/{id}', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{document}/telecharger', [DocumentController::class, 'telecharger'])->name('documents.telecharger');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});

// Module Commercial — CRM léger avec son propre dashboard (dashboard-commercial) et son propre menu.
Route::get('dashboard-commercial', [CommercialDashboardController::class, 'index'])->name('commercial.dashboard');

Route::prefix('commercial')->name('commercial.')->group(function () {
    Route::get('prospects', [ProspectController::class, 'index'])->name('prospects.index');
    Route::get('prospects/{prospect}', [ProspectController::class, 'show'])->name('prospects.show');
    Route::post('prospects', [ProspectController::class, 'store'])->name('prospects.store');
    Route::put('prospects/{prospect}', [ProspectController::class, 'update'])->name('prospects.update');
    Route::delete('prospects/{prospect}', [ProspectController::class, 'destroy'])->name('prospects.destroy');
    Route::post('prospects/{prospect}/statut', [ProspectController::class, 'changerStatut'])->name('prospects.changer-statut');
    Route::post('prospects/{prospect}/convertir-location', [ProspectController::class, 'convertirEnLocation'])->name('prospects.convertir-location');

    Route::post('prospects/{prospect}/activites', [ActiviteController::class, 'store'])->name('activites.store');

    Route::get('parametres', [ParametreCommercialController::class, 'index'])->name('parametres.index');
    Route::post('sources', [SourceController::class, 'store'])->name('sources.store');
    Route::put('sources/{source}', [SourceController::class, 'update'])->name('sources.update');
    Route::delete('sources/{source}', [SourceController::class, 'destroy'])->name('sources.destroy');
    Route::post('types-demande', [TypeDemandeController::class, 'store'])->name('types-demande.store');
    Route::put('types-demande/{type}', [TypeDemandeController::class, 'update'])->name('types-demande.update');
    Route::delete('types-demande/{type}', [TypeDemandeController::class, 'destroy'])->name('types-demande.destroy');

    Route::get('partenaires', [PartenaireController::class, 'index'])->name('partenaires');
    Route::post('partenaires', [PartenaireController::class, 'store'])->name('partenaires.store');
    Route::put('partenaires/{partenaire}', [PartenaireController::class, 'update'])->name('partenaires.update');
    Route::delete('partenaires/{partenaire}', [PartenaireController::class, 'destroy'])->name('partenaires.destroy');

    Route::get('rapports', [RapportController::class, 'index'])->name('rapports');
    Route::get('rapports/export', [RapportController::class, 'export'])->name('rapports.export');

    Route::get('biens-disponibles', [BienDisponibleController::class, 'index'])->name('biens-disponibles');

    Route::get('agenda', [AgendaController::class, 'index'])->name('agenda');
    Route::post('agenda', [AgendaController::class, 'store'])->name('agenda.store');
    Route::put('agenda/{rendezVous}', [AgendaController::class, 'update'])->name('agenda.update');
    Route::delete('agenda/{rendezVous}', [AgendaController::class, 'destroy'])->name('agenda.destroy');
});

Route::get('{any}', [DashboardController::class, 'index'])->where('any', '.*'); // Catch-all route for the dashboard.

}); // fin Route::middleware('auth')
