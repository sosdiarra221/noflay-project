<?php

namespace App\Services\Documents;

use App\Models\Bien;
use App\Models\ContratGerance;
use App\Models\ContratLocation;
use App\Models\Documents\Document;
use App\Models\Documents\DocumentRevision;
use App\Models\Documents\DocumentTemplate;
use App\Services\Locative\NumeroService;
use Illuminate\Support\Facades\DB;

class DocumentGenerationService
{
    public function __construct(
        protected DocumentVariableRegistry $registry,
        protected DocumentRenderer $renderer,
    ) {
    }

    /**
     * Détermine le type de document à générer pour un contrat de location.
     *
     * Heuristique (choix assumé — l'application n'a pas aujourd'hui de champ explicite
     * "habitation vs commercial" sur Bien/ContratLocation) : on se base sur le nom de la
     * catégorie du bien loué. Les catégories usuellement associées à un usage commercial
     * ("Local commercial", "Bureau", "Boutique"...) orientent vers LOCATION_COMMERCIAL ;
     * toute autre catégorie (ou absence de catégorie) est traitée comme une location
     * d'habitation. À ajuster si le modèle Bien gagne un jour un champ dédié.
     */
    public function typePourContratLocation(ContratLocation $contrat): string
    {
        $nomCategorie = mb_strtolower($contrat->bien?->categorie?->nom ?? '');

        $motsClesCommercial = ['local commercial', 'bureau', 'boutique', 'commerce', 'magasin', 'entrepot', 'entrepôt'];

        foreach ($motsClesCommercial as $motCle) {
            if (str_contains($nomCategorie, $motCle)) {
                return DocumentType::LOCATION_COMMERCIAL;
            }
        }

        return DocumentType::LOCATION_HABITATION;
    }

    /**
     * Génère un Document à partir du modèle actif correspondant au type de document, pour le
     * "documentable" donné (ContratLocation ou ContratGerance). Retourne null si aucun modèle
     * actif n'est configuré pour ce type — l'appelant DOIT gérer ce cas sans faire échouer son
     * propre flux (cf. règle : la génération de document ne doit jamais bloquer la création
     * d'une location/gérance).
     */
    public function generateFor($documentable, string $type): ?Document
    {
        $template = DocumentTemplate::where('code', $type)
            ->where('status', DocumentTemplate::STATUT_ACTIVE)
            ->first();

        if (! $template) {
            return null;
        }

        $version = $template->versions()->where('status', 'active')->first();
        if (! $version) {
            return null;
        }

        $contexte = $this->registry->contexte($documentable);
        $contenuRendu = $this->renderer->render($version->content, $contexte);

        return DB::transaction(function () use ($documentable, $type, $template, $version, $contenuRendu, $contexte) {
            $document = Document::create([
                'reference' => NumeroService::genererNumero(Document::class, 'DOC', 'reference'),
                'type' => $type,
                'title' => $template->name,
                'document_template_id' => $template->id,
                'document_template_version_id' => $version->id,
                'documentable_type' => get_class($documentable),
                'documentable_id' => $documentable->id,
                'content' => $contenuRendu,
                'status' => Document::STATUT_GENERATED,
                'context_snapshot' => $contexte,
                'generated_by_id' => auth()->id(),
                'generated_at' => now(),
            ]);

            DocumentRevision::create([
                'document_id' => $document->id,
                'action' => DocumentRevision::ACTION_CREATION,
                'content_before' => null,
                'content_after' => $document->content,
                'changes' => ['template' => $template->code, 'version' => $version->version],
                'user_id' => auth()->id(),
                'note' => 'Génération initiale à partir du modèle '.$template->name.' (v'.$version->version.').',
            ]);

            return $document;
        });
    }

    /**
     * Édition manuelle d'un document déjà généré : à partir de ce moment, le document est
     * indépendant de son modèle d'origine (le modèle peut évoluer sans jamais impacter ce
     * document). Toute modification crée une ligne document_revisions.
     */
    public function editerContenu(Document $document, string $nouveauContenu, ?string $note = null): Document
    {
        $ancienContenu = $document->content;

        if ($ancienContenu === $nouveauContenu) {
            return $document;
        }

        DB::transaction(function () use ($document, $ancienContenu, $nouveauContenu, $note) {
            $document->update(['content' => $nouveauContenu]);

            DocumentRevision::create([
                'document_id' => $document->id,
                'action' => DocumentRevision::ACTION_EDITION,
                'content_before' => $ancienContenu,
                'content_after' => $nouveauContenu,
                'user_id' => auth()->id(),
                'note' => $note,
            ]);
        });

        return $document->fresh();
    }

    /**
     * Change le statut d'un Document déjà généré (draft/generated/review/validated/signed/
     * archived/cancelled) en conservant une trace dans document_revisions.
     */
    public function changerStatut(Document $document, string $statut, ?string $note = null): Document
    {
        $ancienStatut = $document->status;

        if ($ancienStatut === $statut) {
            return $document;
        }

        DB::transaction(function () use ($document, $ancienStatut, $statut, $note) {
            $document->update(['status' => $statut]);

            DocumentRevision::create([
                'document_id' => $document->id,
                'action' => DocumentRevision::ACTION_CHANGEMENT_STATUT,
                'changes' => ['statut_avant' => $ancienStatut, 'statut_apres' => $statut],
                'user_id' => auth()->id(),
                'note' => $note,
            ]);
        });

        return $document->fresh();
    }
}
