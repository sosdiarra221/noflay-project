<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleActif
{
    /**
     * Bloque l'accès à un module désactivé depuis l'écran "Modules" (Administration), tout en
     * laissant passer les modules jamais enregistrés en base (fail-open, cf. Module::estActif()).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $cle): Response
    {
        if (! Module::estActif($cle)) {
            abort(403, 'Ce module a été désactivé par un administrateur.');
        }

        return $next($request);
    }
}
