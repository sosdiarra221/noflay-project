<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Documents\DocumentTemplate;
use App\Models\Documents\DocumentTemplateVersion;
use App\Services\Documents\DocumentPdfService;
use App\Services\Documents\DocumentRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DocumentTemplateVersionController extends Controller
{
    public function index(DocumentTemplate $modele)
    {
        Gate::authorize('documents.templates');

        $versions = $modele->versions()->with('createdBy')->get();

        return view('documents.versions.index', compact('modele', 'versions'));
    }

    /**
     * Enregistre le contenu d'un brouillon (jamais une version déjà publiée — cf. règle
     * "ne jamais muter un document déjà généré / une version déjà active en place").
     */
    public function enregistrer(Request $request, DocumentTemplate $modele, DocumentTemplateVersion $version)
    {
        Gate::authorize('documents.templates');
        abort_unless($version->document_template_id === $modele->id, 404);
        abort_unless($version->estBrouillon(), 422, 'Seul un brouillon peut être enregistré ; publiez une nouvelle version pour modifier un modèle actif.');

        $data = $request->validate([
            'content' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $version->update($data);

        return back()->with('success', 'Brouillon enregistré avec succès.');
    }

    /**
     * Publie une version brouillon : elle devient LA version active du modèle, et l'ancienne
     * version active (s'il y en a une) est démobilisée en "inactive" — jamais supprimée, elle
     * reste consultable dans l'historique des versions.
     */
    public function publier(Request $request, DocumentTemplate $modele, DocumentTemplateVersion $version)
    {
        Gate::authorize('documents.templates');
        abort_unless($version->document_template_id === $modele->id, 404);
        abort_unless($version->estBrouillon(), 422, 'Seul un brouillon peut être publié.');

        if ($request->filled('content')) {
            $version->content = $request->input('content');
        }

        DB::transaction(function () use ($modele, $version) {
            $modele->versions()
                ->where('status', DocumentTemplateVersion::STATUT_ACTIVE)
                ->update(['status' => DocumentTemplateVersion::STATUT_INACTIVE]);

            $version->status = DocumentTemplateVersion::STATUT_ACTIVE;
            $version->published_at = now();
            $version->save();

            $modele->update(['status' => DocumentTemplate::STATUT_ACTIVE]);
        });

        return redirect()->route('documents.modeles.index')->with('success', 'Version '.$version->version.' publiée et activée avec succès.');
    }

    public function apercu(DocumentTemplate $modele, DocumentTemplateVersion $version)
    {
        Gate::authorize('documents.templates');
        abort_unless($version->document_template_id === $modele->id, 404);

        $renderer = new DocumentRenderer();
        $html = $renderer->render($version->content, []);

        $pdf = (new DocumentPdfService())->pourHtml($html, $modele->name, 'Version '.$version->version);

        return $pdf->stream(str($modele->name)->slug().'-v'.$version->version.'.pdf');
    }

    /**
     * Restaure une ancienne version comme point de départ d'un NOUVEAU brouillon (jamais en
     * réécrivant la version historique elle-même).
     */
    public function restaurer(DocumentTemplate $modele, DocumentTemplateVersion $version)
    {
        Gate::authorize('documents.templates');
        abort_unless($version->document_template_id === $modele->id, 404);

        $modele->versions()->where('status', DocumentTemplateVersion::STATUT_DRAFT)->delete();

        $nouveauBrouillon = DocumentTemplateVersion::create([
            'document_template_id' => $modele->id,
            'version' => $modele->prochainNumeroVersion(),
            'status' => DocumentTemplateVersion::STATUT_DRAFT,
            'content' => $version->content,
            'created_by_id' => auth()->id(),
            'notes' => 'Restauré depuis la version '.$version->version.'.',
        ]);

        return redirect()->route('documents.modeles.edit', $modele)->with('success', 'Version '.$version->version.' restaurée en tant que nouveau brouillon.');
    }
}
