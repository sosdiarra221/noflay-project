<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function index()
    {
        return view('profil', ['utilisateur' => auth()->user()]);
    }

    public function updateInformations(Request $request)
    {
        $utilisateur = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($utilisateur)],
            'identifiant' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'identifiant')->ignore($utilisateur)],
        ]);

        $data['identifiant'] = $data['identifiant'] ?: null;

        $utilisateur->update($data);

        return back()->with('success', 'Informations mises à jour avec succès.');
    }

    public function updatePhoto(Request $request)
    {
        $utilisateur = auth()->user();

        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        if ($utilisateur->photo) {
            Storage::disk('public')->delete($utilisateur->photo);
        }

        $chemin = $request->file('photo')->store('avatars', 'public');
        $utilisateur->update(['photo' => $chemin]);

        return back()->with('success', 'Photo de profil mise à jour avec succès.');
    }

    public function updateCodePin(Request $request)
    {
        $utilisateur = auth()->user();

        $data = $request->validate([
            'mot_de_passe_actuel' => ['required', 'string'],
            'code_pin' => ['required', 'digits:4', 'confirmed'],
        ]);

        if (! Hash::check($data['mot_de_passe_actuel'], $utilisateur->password)) {
            return back()->withErrors(['mot_de_passe_actuel' => 'Mot de passe actuel incorrect.'])->with('onglet', 'pin');
        }

        $utilisateur->update(['code_pin' => Hash::make($data['code_pin'])]);

        return back()->with('success', 'Code PIN mis à jour avec succès.');
    }

    public function updateMotDePasse(Request $request)
    {
        $utilisateur = auth()->user();

        $data = $request->validate([
            'mot_de_passe_actuel' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ]);

        if (! Hash::check($data['mot_de_passe_actuel'], $utilisateur->password)) {
            return back()->withErrors(['mot_de_passe_actuel' => 'Mot de passe actuel incorrect.'])->with('onglet', 'mot-de-passe');
        }

        $utilisateur->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }
}
