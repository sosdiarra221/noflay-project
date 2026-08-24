<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use App\Models\Reglage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Déconnecte automatiquement un utilisateur resté inactif au-delà de la durée configurée
 * (Reglage::duree_inactivite_minutes, réglable depuis Direction & Administration > Sécurité)
 * et le redirige vers l'écran de verrouillage (ré-authentification par code PIN uniquement).
 */
class VerifierInactivite
{
    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = Auth::user();

        if ($utilisateur) {
            $dureeMinutes = (int) (Reglage::courant()->duree_inactivite_minutes ?? 15);

            if ($utilisateur->derniere_activite_at && $utilisateur->derniere_activite_at->diffInMinutes(now()) > $dureeMinutes) {
                Cookie::queue(AuthController::COOKIE_VERROUILLAGE, (string) $utilisateur->id, 120);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('verrouillage');
            }

            // Ne met à jour la dernière activité qu'une fois par minute pour limiter les écritures.
            if (! $utilisateur->derniere_activite_at || $utilisateur->derniere_activite_at->diffInSeconds(now()) >= 60) {
                $utilisateur->timestamps = false;
                $utilisateur->update(['derniere_activite_at' => now()]);
            }
        }

        return $next($request);
    }
}
