<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Rh\Employe;
use App\Models\Rh\EmployeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class EmployeDocumentController extends Controller
{
    const TYPES = [
        'cv' => 'CV',
        'diplome' => 'Diplôme',
        'piece_identite' => "Pièce d'identité",
        'certificat_medical' => 'Certificat médical',
        'attestation' => 'Attestation',
        'autre' => 'Autre',
    ];

    public function store(Request $request, Employe $employe)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'type_document' => ['required', 'string', 'in:'.implode(',', array_keys(self::TYPES))],
            'fichier' => ['required', 'file', 'max:10240'],
        ]);

        $fichier = $request->file('fichier');
        $chemin = $fichier->store('employes-documents', 'public');

        $employe->documents()->create([
            'type_document' => $data['type_document'],
            'nom_fichier' => $fichier->getClientOriginalName(),
            'chemin_fichier' => $chemin,
            'type_mime' => $fichier->getClientMimeType(),
            'taille' => $fichier->getSize(),
            'ajoute_par_id' => auth()->id(),
        ]);

        return back()->with('success', 'Document ajouté avec succès.');
    }

    public function apercu(EmployeDocument $document)
    {
        Gate::authorize('rh.donnees-sensibles');

        return Storage::disk('public')->response($document->chemin_fichier, $document->nom_fichier);
    }

    public function destroy(EmployeDocument $document)
    {
        Gate::authorize('rh.gerer');

        Storage::disk('public')->delete($document->chemin_fichier);
        $document->delete();

        return back()->with('success', 'Document supprimé avec succès.');
    }
}
