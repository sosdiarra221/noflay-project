<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    const STATUTS = ['actif', 'suspendu'];

    /**
     * Ces colonnes vivent en vraies colonnes SQL sur `tenants` plutôt que noyées dans le champ
     * JSON `data` — pratique pour filtrer/trier le registre des sociétés depuis l'écran vendeur.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'nom_pme',
            'statut',
            'plan',
        ];
    }
}
