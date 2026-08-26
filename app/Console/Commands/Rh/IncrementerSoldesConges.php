<?php

namespace App\Console\Commands\Rh;

use App\Models\Rh\Employe;
use Illuminate\Console\Command;

class IncrementerSoldesConges extends Command
{
    protected $signature = 'rh:incrementer-soldes-conges';

    protected $description = "Ajoute 2 jours au solde de congé de chaque employé actif sous contrat CDI ou CDD actif — à exécuter le 1er de chaque mois via le planificateur";

    const JOURS_PAR_MOIS = 2;

    public function handle(): int
    {
        $employes = Employe::actifs()
            ->whereHas('contratActif', fn ($q) => $q->whereIn('type_contrat', ['cdi', 'cdd']))
            ->get();

        foreach ($employes as $employe) {
            $employe->increment('solde_conges', self::JOURS_PAR_MOIS);
        }

        $this->info($employes->count().' employé(s) crédité(s) de '.self::JOURS_PAR_MOIS.' jour(s) de congé.');

        return self::SUCCESS;
    }
}
