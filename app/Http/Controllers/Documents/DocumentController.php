<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Documents\Document;
use App\Models\Documents\DocumentTemplate;
use App\Services\Documents\DocumentGenerationService;
use App\Services\Documents\DocumentPdfService;
use App\Services\Documents\DocumentType;
use App\Services\Documents\DocumentVariableRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('documents.gerer');

        $documents = Document::with(['template', 'documentable'])
            ->when($request->filled('document_template_id'), fn ($q) => $q->where('document_template_id', $request->document_template_id))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('statut'), fn ($q) => $q->where('status', $request->statut))
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(function ($q2) use ($terme) {
                    $q2->where('reference', 'like', "%{$terme}%")
                        ->orWhere('title', 'like', "%{$terme}%");
                });
            })
            ->latest('generated_at')
            ->get();

        $modeles = DocumentTemplate::orderBy('name')->get();

        return view('documents.generes.index', [
            'documents' => $documents,
            'modeles' => $modeles,
            'types' => DocumentType::tous(),
        ]);
    }

    public function edit(Document $document)
    {
        Gate::authorize('documents.gerer');

        return view('documents.generes.edit', [
            'document' => $document,
            'groupesVariables' => DocumentVariableRegistry::groupes(),
        ]);
    }

    public function update(Request $request, Document $document, DocumentGenerationService $service)
    {
        Gate::authorize('documents.gerer');

        $data = $request->validate([
            'content' => ['required', 'string'],
            'status' => ['nullable', 'string', 'in:'.implode(',', Document::STATUTS)],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $service->editerContenu($document, $data['content'], $data['note'] ?? 'Modification manuelle du contenu du document.');

        if (! empty($data['status']) && $data['status'] !== $document->status) {
            $service->changerStatut($document->fresh(), $data['status'], $data['note'] ?? null);
        }

        return redirect()->route('documents.generes.index')->with('success', 'Document mis à jour avec succès. Une nouvelle entrée a été ajoutée à son historique.');
    }

    public function apercu(Document $document)
    {
        Gate::authorize('documents.gerer');

        $pdf = (new DocumentPdfService())->pourDocument($document);

        return $pdf->stream($document->reference.'.pdf');
    }

    public function telecharger(Document $document)
    {
        Gate::authorize('documents.gerer');

        $pdf = (new DocumentPdfService())->pourDocument($document);

        return $pdf->download($document->reference.'.pdf');
    }

    public function historique(Document $document)
    {
        Gate::authorize('documents.gerer');

        $document->load(['revisions.user', 'template', 'version']);

        return view('documents.generes.historique', compact('document'));
    }
}
