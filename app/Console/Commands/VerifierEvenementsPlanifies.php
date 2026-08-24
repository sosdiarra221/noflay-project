<?php

namespace App\Console\Commands;

use App\Events\ContratBientotExpire;
use App\Events\EcheanceProche;
use App\Events\LoyerEnRetard;
use App\Models\ContratLocation;
use App\Models\EcheanceLoyer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifierEvenementsPlanifies extends Command
{
    protected $signature = 'notifications:verifier-evenements-planifies';

    protected $description = "Détecte les loyers en retard, les échéances proches et les contrats bientôt expirés, et déclenche les événements métier correspondants (à exécuter quotidiennement via le planificateur)";

    public function handle(): int
    {
        $this->verifierLoyersEnRetard();
        $this->verifierEcheancesProches();
        $this->verifierContratsBientotExpires();

        return self::SUCCESS;
    }

    protected function verifierLoyersEnRetard(): void
    {
        EcheanceLoyer::where('statut', 'en_retard')
            ->with('contratLocation.location.locataire')
            ->get()
            ->each(function (EcheanceLoyer $echeance) {
                $lien = route('locative.echeances.index', ['loyer' => $echeance->id]);
                $this->declencherSiPasDejaEnvoye('loyer_en_retard', $lien, fn () => event(new LoyerEnRetard($echeance)));
            });
    }

    protected function verifierEcheancesProches(): void
    {
        EcheanceLoyer::whereIn('statut', ['a_venir', 'partiel'])
            ->whereBetween('date_echeance', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
            ->with('contratLocation.location.locataire')
            ->get()
            ->each(function (EcheanceLoyer $echeance) {
                $lien = route('locative.echeances.index', ['loyer' => $echeance->id]);
                $this->declencherSiPasDejaEnvoye('echeance_proche', $lien, fn () => event(new EcheanceProche($echeance)));
            });
    }

    protected function verifierContratsBientotExpires(): void
    {
        ContratLocation::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->with('location.locataire', 'bien')
            ->get()
            ->each(function (ContratLocation $contrat) {
                $lien = route('locative.contrats.show', $contrat);
                $this->declencherSiPasDejaEnvoye('contrat_bientot_expire', $lien, fn () => event(new ContratBientotExpire($contrat)));
            });
    }

    /**
     * Évite de renvoyer la même notification à chaque exécution quotidienne du planificateur :
     * on ne redéclenche l'événement que si aucune notification du même type, pointant vers le
     * même lien (donc la même entité), n'a déjà été envoyée dans les 23 dernières heures.
     */
    protected function declencherSiPasDejaEnvoye(string $type, string $lien, \Closure $declencheur): void
    {
        $dejaEnvoyee = DB::table('notifications')
            ->where('created_at', '>=', now()->subHours(23))
            ->where('data', 'like', '%"type":"'.$type.'"%')
            ->where('data', 'like', '%'.addcslashes($lien, '%_\\').'%')
            ->exists();

        if (! $dejaEnvoyee) {
            $declencheur();
        }
    }
}
