<?php

namespace App\Services\Documents;

use App\Models\Documents\Document;
use App\Models\Reglage;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfInstance;

/**
 * Génère le PDF d'un Document (généré) ou d'un HTML brut, en réutilisant DomPDF et l'habillage
 * A4 / branding déjà utilisés par le reste de l'application (cf. resources/views/locative/pdf).
 */
class DocumentPdfService
{
    public function pourDocument(Document $document): PdfInstance
    {
        $reglage = Reglage::courant();

        return Pdf::loadView('documents.pdf.document', [
            'titre' => $document->title,
            'sousTitre' => $document->reference,
            'contenu' => $document->content,
            'reglage' => $reglage,
        ]);
    }

    public function pourHtml(string $html, string $titre, string $sousTitre = ''): PdfInstance
    {
        $reglage = Reglage::courant();

        return Pdf::loadView('documents.pdf.document', [
            'titre' => $titre,
            'sousTitre' => $sousTitre,
            'contenu' => $html,
            'reglage' => $reglage,
        ]);
    }
}
