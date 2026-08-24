<?php

namespace Database\Seeders;

use App\Models\Bailleur;
use App\Models\Bien;
use App\Models\CategorieBien;
use App\Models\ContratLocation;
use App\Models\Locataire;
use App\Services\Locative\EcheanceLoyerService;
use App\Services\Locative\NumeroService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoLocatifSeeder extends Seeder
{
    /**
     * Reconstitue un jeu de données locatives/financières cohérent : remet à zéro les tables
     * "loyers et encaissements" (paiements, échéances, fiches proforma, cautions, reversements)
     * puis ajoute 6 bailleurs et 10 locataires supplémentaires, chacun avec ses biens, prêts à
     * être utilisés pour créer de nouvelles locations via l'écran habituel.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('paiements')->delete();
        DB::table('echeances_loyer')->delete();
        DB::table('fiches_locatives')->delete();
        DB::table('cautions')->delete();
        DB::table('reversements_bailleurs')->delete();
        Schema::enableForeignKeyConstraints();

        // Régénère des échéances propres pour les contrats déjà existants.
        $echeanceService = app(EcheanceLoyerService::class);
        foreach (ContratLocation::where('statut', 'actif')->get() as $contrat) {
            $echeanceService->genererPourContrat($contrat);
        }

        $categorieHabitation = fn (string $nom) => CategorieBien::firstOrCreate(['nom' => $nom], ['actif' => true]);

        $bailleursData = [
            ['type' => 'particulier', 'prenom' => 'Mamadou', 'nom' => 'Fall', 'telephone' => '771000001'],
            ['type' => 'particulier', 'prenom' => 'Aïssatou', 'nom' => 'Diallo', 'telephone' => '771000002'],
            ['type' => 'entreprise', 'nom' => 'Teranga Immobilier SARL', 'telephone' => '338000003'],
            ['type' => 'particulier', 'prenom' => 'Ousmane', 'nom' => 'Sarr', 'telephone' => '771000004'],
            ['type' => 'particulier', 'prenom' => 'Bineta', 'nom' => 'Ndoye', 'telephone' => '771000005'],
            ['type' => 'entreprise', 'nom' => 'Baobab Foncier & Gestion', 'telephone' => '338000006'],
        ];

        $bailleurs = [];
        foreach ($bailleursData as $data) {
            $bailleurs[] = Bailleur::create($data + [
                'numero' => NumeroService::genererNumero(Bailleur::class, 'BLR'),
                'adresse' => 'Dakar, Sénégal',
                'statut' => 'actif',
            ]);
        }

        $locataireNoms = [
            ['Awa', 'Sow'], ['Ibrahima', 'Ba'], ['Fatou', 'Cissé'], ['Cheikh', 'Gueye'],
            ['Mariama', 'Diouf'], ['Alioune', 'Faye'], ['Khady', 'Sy'], ['Moussa', 'Kane'],
            ['Ndeye', 'Thiam'], ['Abdou', 'Seck'],
        ];

        foreach ($locataireNoms as $index => [$prenom, $nom]) {
            Locataire::create([
                'numero' => NumeroService::genererNumero(Locataire::class, 'LOCT'),
                'prenom' => $prenom,
                'nom' => $nom,
                'telephone' => '76'.str_pad((string) (100000 + $index), 7, '0', STR_PAD_LEFT),
                'type_locataire' => 'particulier',
                'statut' => 'actif',
            ]);
        }

        // Biens : mélange habitation / commercial pour chaque bailleur.
        $biensParBailleur = [
            [['Studio', 'location', 95000], ['Appartement', 'location', 150000]],
            [['Villa', 'location', 350000]],
            [['Local commercial', 'location', 275000], ['Bureau', 'location', 220000]],
            [['Maison', 'location', 180000], ['Chambre', 'location', 60000]],
            [['Boutique', 'location', 130000]],
            [['Appartement', 'location', 175000], ['Local commercial', 'location', 300000], ['Studio', 'location', 85000]],
        ];

        $zones = ['Plateau', 'Mermoz', 'Sacré-Cœur', 'Almadies', 'Yoff', 'Ouakam', 'Point E', 'Liberté 6', 'Ngor', 'Sicap Baobab'];

        foreach ($bailleurs as $i => $bailleur) {
            foreach ($biensParBailleur[$i] as $j => [$nomCategorie, $typeExploitation, $loyer]) {
                Bien::create([
                    'numero' => NumeroService::genererNumero(Bien::class, 'BIEN'),
                    'titre' => $nomCategorie.' — '.$bailleur->nom_complet,
                    'categorie_bien_id' => $categorieHabitation($nomCategorie)->id,
                    'adresse' => 'Dakar, Sénégal',
                    'zone' => $zones[array_rand($zones)],
                    'bailleur_id' => $bailleur->id,
                    'type_exploitation' => $typeExploitation,
                    'loyer_mensuel' => $loyer,
                    'frais_gestion_mode' => 'pourcentage',
                    'frais_gestion_valeur' => 10,
                    'statut' => 'disponible',
                ]);
            }
        }
    }
}
