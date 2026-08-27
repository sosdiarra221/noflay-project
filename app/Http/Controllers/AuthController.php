<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Reglage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    const COOKIE_VERROUILLAGE = 'verrouillage_user';

    /**
     * Page d'accueil de la société (domaine tenant) : redirige un utilisateur déjà connecté
     * vers son tableau de bord, sinon affiche une page de présentation avec un bouton de
     * connexion.
     */
    public function accueil()
    {
        if (Auth::check()) {
            return redirect($this->redirectionTableauDeBord(Auth::user()));
        }

        $reglage = Reglage::courant();

        return view('landing', compact('reglage'));
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->redirectionTableauDeBord(Auth::user()));
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

        return redirect()->intended($this->redirectionTableauDeBord($utilisateur));
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

        return redirect()->intended($this->redirectionTableauDeBord($utilisateur));
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

        return redirect($this->redirectionTableauDeBord($utilisateur));
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

    /**
     * Détermine le tableau de bord "par défaut" d'un utilisateur : le premier module, dans
     * l'ordre du catalogue (config/modules.php), à la fois actif pour la société (abonnement)
     * et accessible à son rôle. Reproduit le comportement historique (tout le monde atterrissait
     * sur Locative) pour un utilisateur ayant accès à tout, tout en redirigeant un utilisateur
     * plus spécialisé (ex. RH seul) directement vers le module qu'il utilise réellement.
     */
    protected function redirectionTableauDeBord(User $utilisateur): string
    {
        $tableauxDeBord = [
            'locative' => 'locative.dashboard',
            'finance' => 'finance.dashboard',
            'commercial' => 'commercial.dashboard',
            'documents' => 'documents.dashboard',
            'facturation' => 'facturation.dashboard',
            'rh' => 'rh.dashboard',
            'administration' => 'administration.dashboard',
        ];

        // Modules sans permission dédiée (locative, commercial, facturation) : on se fie
        // uniquement à l'activation du module pour la société, comme le fait aujourd'hui
        // chacun de ces tableaux de bord (aucun Gate::authorize() sur leur contrôleur).
        $abilites = [
            'rh' => 'rh.consulter',
            'finance' => 'finance.consulter',
            'documents' => 'documents.gerer',
            'administration' => 'administration.gerer',
        ];

        foreach ($tableauxDeBord as $module => $nomRoute) {
            if (! Module::estActif($module)) {
                continue;
            }

            if (isset($abilites[$module]) && ! $utilisateur->can($abilites[$module])) {
                continue;
            }

            return route($nomRoute);
        }

        return route('profil.index');
    }
}
