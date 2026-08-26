<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActif
{
    /**
     * Bloque toute la société (même la page de connexion) si son compte a été suspendu par
     * l'éditeur du logiciel depuis l'espace central (colonne `statut` de la table `tenants`).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenant() && tenant('statut') === 'suspendu') {
            abort(403, "Ce compte a été suspendu par l'éditeur du logiciel. Contactez-le pour plus d'informations.");
        }

        return $next($request);
    }
}
