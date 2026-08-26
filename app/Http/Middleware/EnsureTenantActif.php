<?php

namespace App\Http\Middleware;

use App\Models\Licence;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActif
{
    /**
     * Bloque toute la société (même la page de connexion) si :
     * - son compte a été suspendu manuellement par l'éditeur du logiciel (statut `suspendu`) ;
     * - ou si sa licence la plus récente est expirée ou absente (abonnement à durée limitée,
     *   voir l'espace central "Administrateur du logiciel").
     *
     * Si la licence expire dans 5 jours ou moins (mais n'est pas encore passée), la vue reçoit
     * une alerte à afficher (modal, voir partials/licence-alerte-modal.blade.php) plutôt qu'un
     * blocage.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! tenant()) {
            return $next($request);
        }

        if (tenant('statut') === 'suspendu') {
            abort(403, "Ce compte a été suspendu par l'éditeur du logiciel. Contactez-le pour plus d'informations.");
        }

        $licence = Licence::where('tenant_id', tenant('id'))->orderByDesc('date_fin')->first();

        if (! $licence || $licence->estExpiree()) {
            abort(403, "La licence de cette société a expiré. Contactez le support ou l'éditeur du logiciel pour la renouveler.");
        }

        if ($licence->joursRestants() <= 5) {
            View::share('licenceAlerte', [
                'jours' => $licence->joursRestants(),
                'date_fin' => $licence->date_fin,
            ]);
        }

        return $next($request);
    }
}
