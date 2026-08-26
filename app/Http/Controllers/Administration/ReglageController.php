<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Devise;
use App\Models\Reglage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReglageController extends Controller
{
    public function index()
    {
        Gate::authorize('administration.gerer');

        $reglage = Reglage::courant();
        $devises = Devise::orderBy('nom')->get();

        return view('administration.reglages', compact('reglage', 'devises'));
    }

    public function updateGeneral(Request $request)
    {
        Gate::authorize('administration.gerer');

        $reglage = Reglage::courant();

        $data = $request->validate([
            'nom_societe' => ['required', 'string', 'max:255'],
            'ninea' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'site_web' => ['nullable', 'string', 'max:255'],
            'devise_id' => ['nullable', 'exists:devises,id'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $dossier = public_path('uploads/logos');
            if (! is_dir($dossier)) {
                mkdir($dossier, 0755, true);
            }

            $fichier = $request->file('logo');
            $nomFichier = uniqid('logo_').'.'.$fichier->getClientOriginalExtension();
            $fichier->move($dossier, $nomFichier);

            $data['logo'] = 'uploads/logos/'.$nomFichier;
        }

        $reglage->update($data);

        return back()->with('success', 'Les informations de la société ont été mises à jour.');
    }

    public function updateSmtp(Request $request)
    {
        Gate::authorize('administration.gerer');

        $reglage = Reglage::courant();

        $data = $request->validate([
            'smtp_type' => ['required', 'string', 'in:smtp,sendmail,mailgun,ses,postmark,resend,log'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'string', 'max:10'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'string', 'in:tls,ssl,none'],
            'smtp_from_address' => ['nullable', 'email', 'max:255'],
            'smtp_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($data['smtp_password'])) {
            unset($data['smtp_password']);
        }

        $reglage->update($data);

        return back()->with('success', 'La configuration SMTP a été mise à jour.');
    }
}
