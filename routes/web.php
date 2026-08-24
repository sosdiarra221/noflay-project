<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\DeviseController;
use App\Http\Controllers\ReglageController;
use App\Http\Controllers\Locative\LocativeDashboardController;
use App\Http\Controllers\Locative\ParametreLocativeController;
use App\Http\Controllers\Locative\CategorieBienController;
use App\Http\Controllers\Locative\CategorieDepenseController;
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
use App\Http\Controllers\Locative\VersementBailleurController;
use App\Http\Controllers\Locative\CorbeilleController;
use App\Http\Controllers\Locative\JournalActiviteController;
use App\Http\Controllers\Locative\DocumentController;
use App\Http\Controllers\Locative\EncaissementController;
use App\Http\Controllers\Locative\FicheLocativeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Administration\AdministrationDashboardController;
use App\Http\Controllers\Administration\UtilisateurController;
use App\Http\Controllers\Administration\RoleController;
use App\Http\Controllers\Administration\SecuriteController;
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
use App\Http\Controllers\Documents\DocumentsDashboardController;
use App\Http\Controllers\Documents\DocumentTemplateController;
use App\Http\Controllers\Documents\DocumentTemplateVersionController;
use App\Http\Controllers\Documents\DocumentController as DocumentGenereController;
use App\Http\Controllers\Finance\BailleurFinanceController;
use App\Http\Controllers\Finance\CautionController;
use App\Http\Controllers\Finance\DepenseController;
use App\Http\Controllers\Finance\FinanceDashboardController;
use App\Http\Controllers\Finance\LocataireFinanceController;
use App\Http\Controllers\Finance\JournalCaisseController;
use App\Http\Controllers\Finance\RevenuController;
use App\Http\Controllers\Finance\ReversementFinanceController;
use App\Http\Controllers\Finance\TaxeController;

Route::get('connexion', [AuthController::class, 'showLogin'])->name('login');
Route::post('connexion', [AuthController::class, 'login'])->name('login.store');
Route::post('connexion-pin', [AuthController::class, 'loginParPin'])->name('login.pin');

Route::get('verrouillage', [AuthController::class, 'showVerrouillage'])->name('verrouillage');
Route::post('verrouillage', [AuthController::class, 'deverrouiller'])->name('verrouillage.store');

Route::middleware(['auth', 'inactivite'])->group(function () {

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
    Route::put('parametres/taxes', [ParametreLocativeController::class, 'updateTaxes'])->name('parametres.taxes');
    Route::post('categories-biens', [CategorieBienController::class, 'store'])->name('categories-biens.store');
    Route::put('categories-biens/{categorie}', [CategorieBienController::class, 'update'])->name('categories-biens.update');
    Route::delete('categories-biens/{categorie}', [CategorieBienController::class, 'destroy'])->name('categories-biens.destroy');
    Route::post('modes-paiement', [ModePaiementController::class, 'store'])->name('modes-paiement.store');
    Route::put('modes-paiement/{mode}', [ModePaiementController::class, 'update'])->name('modes-paiement.update');
    Route::delete('modes-paiement/{mode}', [ModePaiementController::class, 'destroy'])->name('modes-paiement.destroy');
    Route::post('categories-depense', [CategorieDepenseController::class, 'store'])->name('categories-depense.store');
    Route::put('categories-depense/{categorieDepense}', [CategorieDepenseController::class, 'update'])->name('categories-depense.update');
    Route::delete('categories-depense/{categorieDepense}', [CategorieDepenseController::class, 'destroy'])->name('categories-depense.destroy');

    Route::get('bailleurs', [BailleurController::class, 'index'])->name('bailleurs.index');
    Route::get('bailleurs/{bailleur}', [BailleurController::class, 'show'])->name('bailleurs.show');
    Route::post('bailleurs', [BailleurController::class, 'store'])->name('bailleurs.store');
    Route::put('bailleurs/{bailleur}', [BailleurController::class, 'update'])->name('bailleurs.update');
    Route::delete('bailleurs/{bailleur}', [BailleurController::class, 'destroy'])->name('bailleurs.destroy');

    Route::get('gerances', [ContratGeranceController::class, 'index'])->name('gerances.index');
    Route::get('gerances/creer', [ContratGeranceController::class, 'create'])->name('gerances.create');
    Route::get('gerances/{gerance}', [ContratGeranceController::class, 'show'])->name('gerances.show');
    Route::get('gerances/{gerance}/pdf', [ContratGeranceController::class, 'pdf'])->name('gerances.pdf');
    Route::get('gerances/{gerance}/apercu', [ContratGeranceController::class, 'apercu'])->name('gerances.apercu');
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
    Route::get('contrats/{contrat}/apercu', [ContratLocationController::class, 'apercu'])->name('contrats.apercu');
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

    Route::get('versements', [VersementBailleurController::class, 'index'])->name('versements.index');
    Route::post('versements', [VersementBailleurController::class, 'store'])->name('versements.store');
    Route::delete('versements/{versement}', [VersementBailleurController::class, 'destroy'])->name('versements.destroy');

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

// Module Gestion Document — sous-application avec son propre dashboard et son propre menu.
// Moteur de modèles et de génération de documents (contrats, mandats...).
Route::prefix('gestion-documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentsDashboardController::class, 'index'])->name('dashboard');

    Route::get('modeles', [DocumentTemplateController::class, 'index'])->name('modeles.index');
    Route::get('modeles/creer', [DocumentTemplateController::class, 'create'])->name('modeles.create');
    Route::post('modeles', [DocumentTemplateController::class, 'store'])->name('modeles.store');
    Route::get('modeles/{modele}/editer', [DocumentTemplateController::class, 'edit'])->name('modeles.edit');
    Route::get('modeles/{modele}/apercu', [DocumentTemplateController::class, 'apercu'])->name('modeles.apercu');
    Route::post('modeles/{modele}/dupliquer', [DocumentTemplateController::class, 'dupliquer'])->name('modeles.dupliquer');
    Route::delete('modeles/{modele}', [DocumentTemplateController::class, 'destroy'])->name('modeles.destroy');

    Route::get('modeles/{modele}/versions', [DocumentTemplateVersionController::class, 'index'])->name('versions.index');
    Route::post('modeles/{modele}/versions/{version}/enregistrer', [DocumentTemplateVersionController::class, 'enregistrer'])->name('versions.enregistrer');
    Route::post('modeles/{modele}/versions/{version}/publier', [DocumentTemplateVersionController::class, 'publier'])->name('versions.publier');
    Route::get('modeles/{modele}/versions/{version}/apercu', [DocumentTemplateVersionController::class, 'apercu'])->name('versions.apercu');
    Route::post('modeles/{modele}/versions/{version}/restaurer', [DocumentTemplateVersionController::class, 'restaurer'])->name('versions.restaurer');

    Route::get('generes', [DocumentGenereController::class, 'index'])->name('generes.index');
    Route::get('generes/{document}/editer', [DocumentGenereController::class, 'edit'])->name('generes.edit');
    Route::put('generes/{document}', [DocumentGenereController::class, 'update'])->name('generes.update');
    Route::get('generes/{document}/apercu', [DocumentGenereController::class, 'apercu'])->name('generes.apercu');
    Route::get('generes/{document}/telecharger', [DocumentGenereController::class, 'telecharger'])->name('generes.telecharger');
    Route::get('generes/{document}/historique', [DocumentGenereController::class, 'historique'])->name('generes.historique');
});

// Module Finance — sous-application avec son propre dashboard et son propre menu.
Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [FinanceDashboardController::class, 'index'])->name('dashboard');

    Route::get('revenus', [RevenuController::class, 'index'])->name('revenus.index');

    Route::get('reversements', [ReversementFinanceController::class, 'index'])->name('reversements.index');
    Route::post('reversements/marquer-verse', [ReversementFinanceController::class, 'marquerVerse'])->name('reversements.marquer-verse');
    Route::get('reversements/historique', [ReversementFinanceController::class, 'historique'])->name('reversements.historique');
    Route::get('reversements/{reversement}/bordereau', [ReversementFinanceController::class, 'apercuBordereau'])->name('reversements.bordereau');
    Route::get('reversements/{reversement}/bordereau/telecharger', [ReversementFinanceController::class, 'telechargerBordereau'])->name('reversements.bordereau-telecharger');

    Route::get('taxes', [TaxeController::class, 'index'])->name('taxes.index');

    Route::get('cautions', [CautionController::class, 'index'])->name('cautions.index');
    Route::post('cautions/{caution}/restituer', [CautionController::class, 'restituer'])->name('cautions.restituer');

    Route::get('journal-caisse', [JournalCaisseController::class, 'index'])->name('journal-caisse.index');

    Route::get('depenses', [DepenseController::class, 'index'])->name('depenses.index');
    Route::get('depenses/creer', [DepenseController::class, 'create'])->name('depenses.create');
    Route::post('depenses', [DepenseController::class, 'store'])->name('depenses.store');
    Route::get('depenses/{depense}', [DepenseController::class, 'show'])->name('depenses.show');
    Route::post('depenses/{depense}/soumettre', [DepenseController::class, 'soumettre'])->name('depenses.soumettre');
    Route::post('depenses/{depense}/approuver', [DepenseController::class, 'approuver'])->name('depenses.approuver');
    Route::post('depenses/{depense}/refuser', [DepenseController::class, 'refuser'])->name('depenses.refuser');
    Route::post('depenses/{depense}/demarrer-intervention', [DepenseController::class, 'demarrerIntervention'])->name('depenses.demarrer-intervention');
    Route::post('depenses/{depense}/facture-recue', [DepenseController::class, 'factureRecue'])->name('depenses.facture-recue');
    Route::post('depenses/{depense}/marquer-a-payer', [DepenseController::class, 'marquerAPayer'])->name('depenses.marquer-a-payer');
    Route::post('depenses/{depense}/payer', [DepenseController::class, 'payer'])->name('depenses.payer');
    Route::post('depenses/{depense}/cloturer', [DepenseController::class, 'cloturer'])->name('depenses.cloturer');

    Route::get('locataires', [LocataireFinanceController::class, 'index'])->name('locataires.index');
    Route::get('locataires/{locataire}', [LocataireFinanceController::class, 'show'])->name('locataires.show');

    Route::get('bailleurs', [BailleurFinanceController::class, 'index'])->name('bailleurs.index');
    Route::get('bailleurs/{bailleur}', [BailleurFinanceController::class, 'show'])->name('bailleurs.show');
});

// Module Direction & Administration — sous-application avec son propre dashboard et son propre menu.
Route::prefix('administration')->name('administration.')->group(function () {
    Route::get('/', [AdministrationDashboardController::class, 'index'])->name('dashboard');

    Route::get('utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
    Route::get('utilisateurs/creer', [UtilisateurController::class, 'create'])->name('utilisateurs.create');
    Route::post('utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store');
    Route::get('utilisateurs/{utilisateur}/modifier', [UtilisateurController::class, 'edit'])->name('utilisateurs.edit');
    Route::put('utilisateurs/{utilisateur}', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
    Route::post('utilisateurs/{utilisateur}/statut', [UtilisateurController::class, 'toggleStatut'])->name('utilisateurs.toggle-statut');
    Route::delete('utilisateurs/{utilisateur}', [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}/modifier', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('securite', [SecuriteController::class, 'index'])->name('securite.index');
    Route::put('securite', [SecuriteController::class, 'update'])->name('securite.update');
});

Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('recentes', [NotificationController::class, 'recentes'])->name('recentes');
    Route::post('{id}/lu', [NotificationController::class, 'marquerLu'])->name('marquer-lu');
    Route::post('tout-lu', [NotificationController::class, 'marquerToutLu'])->name('marquer-tout-lu');
});

Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/', [ProfilController::class, 'index'])->name('index');
    Route::put('informations', [ProfilController::class, 'updateInformations'])->name('update-informations');
    Route::post('photo', [ProfilController::class, 'updatePhoto'])->name('update-photo');
    Route::put('code-pin', [ProfilController::class, 'updateCodePin'])->name('update-code-pin');
    Route::put('mot-de-passe', [ProfilController::class, 'updateMotDePasse'])->name('update-mot-de-passe');
});

Route::get('{any}', [DashboardController::class, 'index'])->where('any', '.*'); // Catch-all route for the dashboard.

}); // fin Route::middleware('auth')
