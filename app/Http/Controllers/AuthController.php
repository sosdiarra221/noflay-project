<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    const COOKIE_VERROUILLAGE = 'verrouillage_user';

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('locative.dashboard');
        }

        return view('auth-signin');
    }

    /**
     * Connexion classique par mot de passe. Le champ "identifiant" accepte indifféremment
     * une adresse email ou l'identifiant (nom d'utilisateur) choisi pour le compte.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'identifiant' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $utilisateur = $this->trouverUtilisateur($data['identifiant']);

        if (! $utilisateur || ! Hash::check($data['password'], $utilisateur->password)) {
            return back()->withErrors(['identifiant' => 'Identifiants incorrects.'])->onlyInput('identifiant');
        }

        if (! $utilisateur->estActif()) {
            return back()->withErrors(['identifiant' => 'Ce compte a été désactivé. Contactez un administrateur.'])->onlyInput('identifiant');
        }

        Auth::login($utilisateur, $request->boolean('remember'));
        $request->session()->regenerate();
        $utilisateur->update(['derniere_activite_at' => now()]);
        Cookie::queue(Cookie::forget(self::COOKIE_VERROUILLAGE));

        return redirect()->intended(route('locative.dashboard'));
    }

    /**
     * Connexion par code PIN à 4 chiffres, alternative au mot de passe depuis l'écran de
     * connexion (l'utilisateur doit tout de même indiquer son email/identifiant).
     */
    public function loginParPin(Request $request)
    {
        $data = $request->validate([
            'identifiant' => ['required', 'string'],
            'code_pin' => ['required', 'digits:4'],
        ]);

        $utilisateur = $this->trouverUtilisateur($data['identifiant']);

        if (! $utilisateur || ! $utilisateur->code_pin || ! Hash::check($data['code_pin'], $utilisateur->code_pin)) {
            return back()->withErrors(['code_pin' => 'Identifiant ou code PIN incorrect.'])->withInput()->with('onglet', 'pin');
        }

        if (! $utilisateur->estActif()) {
            return back()->withErrors(['code_pin' => 'Ce compte a été désactivé. Contactez un administrateur.'])->with('onglet', 'pin');
        }

        Auth::login($utilisateur);
        $request->session()->regenerate();
        $utilisateur->update(['derniere_activite_at' => now()]);
        Cookie::queue(Cookie::forget(self::COOKIE_VERROUILLAGE));

        return redirect()->intended(route('locative.dashboard'));
    }

    /**
     * Écran de verrouillage affiché après une déconnexion automatique pour inactivité
     * (cf. middleware VerifierInactivite) : ne redemande que le code PIN, sans repasser par
     * l'écran de connexion complet, tant que l'identité de l'utilisateur reste connue via le
     * cookie chiffré posé au moment du verrouillage.
     */
    public function showVerrouillage(Request $request)
    {
        $userId = $request->cookie(self::COOKIE_VERROUILLAGE);

        if (! $userId) {
            return redirect()->route('login');
        }

        $utilisateur = User::with('role')->find($userId);

        if (! $utilisateur || ! $utilisateur->estActif()) {
            return redirect()->route('login');
        }

        return view('auth-verrouillage', ['utilisateur' => $utilisateur]);
    }

    public function deverrouiller(Request $request)
    {
        $userId = $request->cookie(self::COOKIE_VERROUILLAGE);
        abort_unless($userId, 419);

        $utilisateur = User::find($userId);
        abort_unless($utilisateur, 419);

        $data = $request->validate(['code_pin' => ['required', 'digits:4']]);

        if (! $utilisateur->code_pin || ! Hash::check($data['code_pin'], $utilisateur->code_pin)) {
            return back()->withErrors(['code_pin' => 'Code PIN incorrect.']);
        }

        if (! $utilisateur->estActif()) {
            return redirect()->route('login')->withErrors(['identifiant' => 'Ce compte a été désactivé.']);
        }

        Auth::login($utilisateur);
        $request->session()->regenerate();
        $utilisateur->update(['derniere_activite_at' => now()]);
        Cookie::queue(Cookie::forget(self::COOKIE_VERROUILLAGE));

        return redirect()->route('locative.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget(self::COOKIE_VERROUILLAGE));

        return redirect()->route('login');
    }

    protected function trouverUtilisateur(string $identifiant): ?User
    {
        return User::where('email', $identifiant)->orWhere('identifiant', $identifiant)->first();
    }
}
