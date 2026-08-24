<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\ContratGerance;
use App\Models\ContratLocation;
use App\Models\DepenseLocation;
use App\Models\Document;
use App\Models\Locataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected array $modeles = [
        'bailleur' => Bailleur::class,
        'gerance' => ContratGerance::class,
        'contrat' => ContratLocation::class,
        'locataire' => Locataire::class,
        'depense' => DepenseLocation::class,
    ];

    public function store(Request $request, string $type, int $id)
    {
        Gate::authorize($type === 'depense' ? 'finance.gerer' : 'locative.documents');

        $classe = $this->modeles[$type] ?? abort(404);
        $documentable = $classe::findOrFail($id);

        $request->validate([
            'fichier' => ['required', 'file', 'max:10240'],
            'titre' => ['required', 'string', 'max:255'],
            'categorie' => ['required', 'string', 'in:'.implode(',', Document::TYPES)],
        ]);

        $fichier = $request->file('fichier');
        $chemin = $fichier->store('documents', 'public');

        $documentable->documents()->create([
            'titre' => $request->titre,
            'nom_original' => $fichier->getClientOriginalName(),
            'chemin' => $chemin,
            'type_mime' => $fichier->getClientMimeType(),
            'taille' => $fichier->getSize(),
            'categorie' => $request->categorie,
            'uploaded_by_id' => auth()->id(),
        ]);

        return back()->with('success', 'Document ajouté avec succès.');
    }

    public function telecharger(Document $document)
    {
        return Storage::disk('public')->download($document->chemin, $document->nom_original);
    }

    public function destroy(Document $document)
    {
        Gate::authorize($document->documentable_type === DepenseLocation::class ? 'finance.gerer' : 'locative.documents');

        $document->delete();

        return back()->with('success', 'Document supprimé avec succès.');
    }
}
