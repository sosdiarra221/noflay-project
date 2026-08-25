<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Rh\ContratTravail;
use App\Models\Rh\Employe;
use App\Models\Rh\Poste;
use App\Models\Rh\Site;
use App\Models\User;
use App\Services\Locative\NumeroService;
use Illuminate\Database\Seeder;

class RhSeeder extends Seeder
{
    /**
     * Crée une fiche employé pour chaque utilisateur démo déjà en base (un employé par
     * utilisateur, rattaché à son département), avec un premier contrat de travail actif —
     * pour que le module RH soit démontrable immédiatement sans ressaisie.
     */
    public function run(): void
    {
        $siegeSite = Site::firstOrCreate(['nom' => 'Siège — Dakar Plateau'], ['adresse' => 'Avenue Léopold Sédar Senghor, Dakar', 'actif' => true]);
        $agenceSite = Site::firstOrCreate(['nom' => 'Agence — Almadies'], ['adresse' => 'Route des Almadies, Dakar', 'actif' => true]);

        $profils = [
            'Direction' => ['fonction' => 'Directeur Général', 'categorie' => 'staff', 'solde' => 12, 'epouse' => true, 'enfants' => 2],
            'RH' => ['fonction' => 'Responsable RH', 'categorie' => 'staff', 'solde' => 8, 'epouse' => true, 'enfants' => 1],
            'Technique' => ['fonction' => "Chef d'équipe technique", 'categorie' => 'superviseur', 'solde' => 5, 'epouse' => false, 'enfants' => 0],
            'Commercial' => ['fonction' => 'Chargé de clientèle', 'categorie' => 'staff', 'solde' => 3, 'epouse' => false, 'enfants' => 0],
            'Finance' => ['fonction' => 'Comptable', 'categorie' => 'staff', 'solde' => 6.5, 'epouse' => true, 'enfants' => 3],
            'Logistique' => ['fonction' => 'Agent logistique', 'categorie' => 'agent_terrain', 'solde' => 2, 'epouse' => false, 'enfants' => 0],
        ];

        $enfantsExemples = [
            ['nom_complet' => 'Aminata Diagne', 'date_naissance' => '2015-03-12'],
            ['nom_complet' => 'Modou Diagne', 'date_naissance' => '2018-07-04'],
            ['nom_complet' => 'Fatou Diagne', 'date_naissance' => '2020-11-20'],
        ];

        foreach ($profils as $nomDepartement => $profil) {
            $departement = Departement::where('nom', $nomDepartement)->first();
            $utilisateur = User::where('name', $nomDepartement)->first();

            if (! $departement || ! $utilisateur) {
                continue;
            }

            $poste = Poste::firstOrCreate(['nom' => $profil['fonction']], ['actif' => true]);

            $employe = Employe::firstOrCreate(
                ['user_id' => $utilisateur->id],
                [
                    'matricule' => NumeroService::genererNumeroCourt(Employe::class, 'EMP', 'matricule'),
                    'nom' => $utilisateur->name,
                    'prenom' => 'Responsable',
                    'sexe' => 'homme',
                    'date_naissance' => now()->subYears(rand(28, 50))->subDays(rand(0, 300)),
                    'lieu_naissance' => 'Dakar',
                    'situation_matrimoniale' => $profil['epouse'] ? 'marie' : 'celibataire',
                    'telephone' => '77'.rand(1000000, 9999999),
                    'email' => strtolower($nomDepartement).'@employes.local',
                    'adresse' => 'Dakar, Sénégal',
                    'poste_id' => $poste->id,
                    'categorie_fonction' => $profil['categorie'],
                    'departement_id' => $departement->id,
                    'niveau_etude' => 'Bac +4',
                    'intitule_diplome' => 'Licence / Master professionnel',
                    'langues_parlees' => 'Français, Wolof',
                    'langues_lues' => 'Français',
                    'banque' => 'Banque Atlantique',
                    'compte_bancaire' => 'SN'.rand(10, 99).' '.rand(1000, 9999).' '.rand(1000, 9999).' '.rand(1000, 9999),
                    'personne_urgence_nom' => 'Contact urgence '.$nomDepartement,
                    'personne_urgence_telephone' => '78'.rand(1000000, 9999999),
                    'personne_urgence_lien' => 'Frère/Soeur',
                    'solde_conges' => $profil['solde'],
                    'date_embauche' => now()->subYears(rand(1, 5))->subMonths(rand(0, 11)),
                    'statut' => 'actif',
                    'cree_par_id' => $utilisateur->id,
                    'user_id' => $utilisateur->id,
                ]
            );

            if ($employe->wasRecentlyCreated) {
                $employe->sites()->syncWithoutDetaching([$siegeSite->id]);
                if (in_array($profil['categorie'], ['agent_terrain', 'superviseur'])) {
                    $employe->sites()->syncWithoutDetaching([$agenceSite->id]);
                }

                if ($profil['epouse']) {
                    $employe->epouses()->create([
                        'nom_complet' => 'Épouse de '.$employe->prenom.' '.$employe->nom,
                        'telephone' => '76'.rand(1000000, 9999999),
                    ]);
                }

                for ($i = 0; $i < $profil['enfants']; $i++) {
                    $employe->enfants()->create($enfantsExemples[$i]);
                }

                ContratTravail::create([
                    'numero' => NumeroService::genererNumeroCourt(ContratTravail::class, 'CT'),
                    'employe_id' => $employe->id,
                    'type_contrat' => 'cdi',
                    'date_debut' => $employe->date_embauche,
                    'date_prevu_fin' => null,
                    'montant' => rand(150, 600) * 1000,
                    'etat' => 'actif',
                    'cree_par_id' => $utilisateur->id,
                ]);
            }
        }

        // Un CDD arrivant bientôt à échéance, pour démontrer l'alerte du tableau de bord.
        $employeLogistique = Employe::whereHas('departement', fn ($q) => $q->where('nom', 'Logistique'))->first();
        if ($employeLogistique && ! $employeLogistique->contrats()->where('type_contrat', 'cdd')->exists()) {
            ContratTravail::create([
                'numero' => NumeroService::genererNumeroCourt(ContratTravail::class, 'CT'),
                'employe_id' => $employeLogistique->id,
                'type_contrat' => 'cdd',
                'date_debut' => now()->subMonths(11),
                'date_prevu_fin' => now()->addDays(12),
                'montant' => 180000,
                'etat' => 'actif',
                'cree_par_id' => $employeLogistique->user_id,
            ]);
        }
    }
}
