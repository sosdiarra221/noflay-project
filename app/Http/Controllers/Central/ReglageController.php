<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\ReglageEditeur;
use Illuminate\Http\Request;

class ReglageController extends Controller
{
    public function index()
    {
        $reglage = ReglageEditeur::courant();

        return view('central.reglages', compact('reglage'));
    }

    public function updateGeneral(Request $request)
    {
        $reglage = ReglageEditeur::courant();

        $data = $request->validate([
            'nom_application' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
        ]);

        foreach (['logo', 'favicon'] as $champ) {
            if ($request->hasFile($champ)) {
                $data[$champ] = $this->deplacerFichier($request->file($champ));
            } else {
                unset($data[$champ]);
            }
        }

        $reglage->update($data);

        return back()->with('success', "Les réglages de l'application ont été mis à jour.");
    }

    public function updateSmtp(Request $request)
    {
        $reglage = ReglageEditeur::courant();

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

    public function updateIntegrations(Request $request)
    {
        $reglage = ReglageEditeur::courant();

        $data = $request->validate([
            'pusher_app_id' => ['nullable', 'string', 'max:255'],
            'pusher_key' => ['nullable', 'string', 'max:255'],
            'pusher_secret' => ['nullable', 'string', 'max:255'],
            'pusher_cluster' => ['nullable', 'string', 'max:100'],
            'firebase_api_key' => ['nullable', 'string', 'max:255'],
            'firebase_project_id' => ['nullable', 'string', 'max:255'],
            'firebase_messaging_sender_id' => ['nullable', 'string', 'max:255'],
            'firebase_app_id' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($data['pusher_secret'])) {
            unset($data['pusher_secret']);
        }

        $reglage->update($data);

        return back()->with('success', 'Les intégrations ont été mises à jour.');
    }

    protected function deplacerFichier($fichier): string
    {
        $dossier = public_path('uploads/central');
        if (! is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        $nomFichier = uniqid('img_').'.'.$fichier->getClientOriginalExtension();
        $fichier->move($dossier, $nomFichier);

        return 'uploads/central/'.$nomFichier;
    }
}
