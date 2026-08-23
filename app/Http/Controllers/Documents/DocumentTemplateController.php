<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Documents\DocumentTemplate;
use App\Models\Documents\DocumentTemplateVersion;
use App\Services\Documents\DocumentPdfService;
use App\Services\Documents\DocumentRenderer;
use App\Services\Documents\DocumentType;
use App\Services\Documents\DocumentVariableRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DocumentTemplateController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('documents.templates');

        $modeles = DocumentTemplate::with('activeVersion')
            ->withCount('versions')
            ->when($request->filled('categorie'), fn ($q) => $q->where('category', $request->categorie))
            ->when($request->filled('statut'), fn ($q) => $q->where('status', $request->statut))
            ->orderBy('name')
            ->get();

        return view('documents.modeles.index', [
            'modeles' => $modeles,
            'categories' => collect(DocumentType::tous())->pluck('categorie')->unique()->values(),
        ]);
    }

    public function create()
    {
        Gate::authorize('documents.templates');

        return view('documents.modeles.create', [
            'types' => DocumentType::tous(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('documents.templates');

        $data = $request->validate([
            'code' => ['required', 'string', 'max:80', 'unique:document_templates,code', 'regex:/^[A-Z0-9_]+$/'],
            'name' => ['required', 'string', 'max:180'],
            'category' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
        ]);

        $modele = DB::transaction(function () use ($data) {
            $modele = DocumentTemplate::create([
                ...$data,
                'status' => DocumentTemplate::STATUT_DRAFT,
                'created_by_id' => auth()->id(),
            ]);

            DocumentTemplateVersion::create([
                'document_template_id' => $modele->id,
                'version' => 1,
                'status' => DocumentTemplateVersion::STATUT_DRAFT,
                'content' => '<h1>'.e($modele->name).'</h1><p>Commencez à rédiger le modèle...</p>',
                'created_by_id' => auth()->id(),
            ]);

            return $modele;
        });

        return redirect()->route('documents.modeles.edit', $modele)->with('success', 'Modèle créé. Vous pouvez maintenant rédiger son contenu.');
    }

    public function edit(DocumentTemplate $modele)
    {
        Gate::authorize('documents.templates');

        $brouillon = $modele->versions()->where('status', DocumentTemplateVersion::STATUT_DRAFT)->orderByDesc('version')->first();

        if (! $brouillon) {
            $active = $modele->versions()->where('status', DocumentTemplateVersion::STATUT_ACTIVE)->first();

            $brouillon = DocumentTemplateVersion::create([
                'document_template_id' => $modele->id,
                'version' => $modele->prochainNumeroVersion(),
                'status' => DocumentTemplateVersion::STATUT_DRAFT,
                'content' => $active->content ?? '<p></p>',
                'created_by_id' => auth()->id(),
            ]);
        }

        return view('documents.modeles.edit', [
            'modele' => $modele,
            'version' => $brouillon,
            'groupesVariables' => DocumentVariableRegistry::groupes(),
        ]);
    }

    public function apercu(DocumentTemplate $modele)
    {
        Gate::authorize('documents.templates');

        $version = $modele->versions()->where('status', DocumentTemplateVersion::STATUT_ACTIVE)->first()
            ?? $modele->versions()->orderByDesc('version')->first();

        abort_if(! $version, 404, "Ce modèle n'a encore aucune version.");

        // Aperçu "à vide" : aucun documentable réel n'est disponible ici, les variables connues
        // restent donc affichées comme placeholders {{ ... }} (chemin inconnu du contexte vide).
        $renderer = new DocumentRenderer();
        $html = $renderer->render($version->content, []);

        $pdf = (new DocumentPdfService())->pourHtml($html, $modele->name, 'Aperçu — v'.$version->version);

        return $pdf->stream(str($modele->name)->slug().'-apercu.pdf');
    }

    public function dupliquer(DocumentTemplate $modele)
    {
        Gate::authorize('documents.templates');

        $source = $modele->versions()->where('status', DocumentTemplateVersion::STATUT_ACTIVE)->first()
            ?? $modele->versions()->orderByDesc('version')->first();

        $copie = DB::transaction(function () use ($modele, $source) {
            $suffixe = now()->format('YmdHis');

            $copie = DocumentTemplate::create([
                'code' => $modele->code.'_COPIE_'.$suffixe,
                'name' => $modele->name.' (copie)',
                'category' => $modele->category,
                'description' => $modele->description,
                'status' => DocumentTemplate::STATUT_DRAFT,
                'created_by_id' => auth()->id(),
            ]);

            DocumentTemplateVersion::create([
                'document_template_id' => $copie->id,
                'version' => 1,
                'status' => DocumentTemplateVersion::STATUT_DRAFT,
                'content' => $source->content ?? '<p></p>',
                'created_by_id' => auth()->id(),
                'notes' => 'Dupliqué depuis '.$modele->name.' ('.$modele->code.').',
            ]);

            return $copie;
        });

        return redirect()->route('documents.modeles.edit', $copie)->with('success', 'Modèle dupliqué avec succès. Pensez à modifier son code avant publication.');
    }

    public function destroy(Request $request, DocumentTemplate $modele)
    {
        Gate::authorize('documents.templates');

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $modele->motif_suppression = $request->motif_suppression;
        $modele->supprime_par_id = auth()->id();
        $modele->save();
        $modele->delete();

        return redirect()->route('documents.modeles.index')->with('success', 'Modèle archivé avec succès.');
    }
}
