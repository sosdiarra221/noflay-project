<?php

namespace App\Models\Documents;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Document GÉNÉRÉ à partir d'un modèle (App\Models\Documents\DocumentTemplate) ou saisi librement.
 * Ne pas confondre avec App\Models\Document, qui gère les pièces jointes polymorphes existantes
 * (bailleurs/locataires/gérances/contrats) — les deux systèmes coexistent volontairement.
 */
class Document extends Model
{
    use SoftDeletes;
    use Auditable;

    protected string $moduleJournal = 'documents';

    protected $table = 'documents_generes';

    const STATUT_DRAFT = 'draft';
    const STATUT_GENERATED = 'generated';
    const STATUT_REVIEW = 'review';
    const STATUT_VALIDATED = 'validated';
    const STATUT_SIGNED = 'signed';
    const STATUT_ARCHIVED = 'archived';
    const STATUT_CANCELLED = 'cancelled';

    const STATUTS = [
        self::STATUT_DRAFT,
        self::STATUT_GENERATED,
        self::STATUT_REVIEW,
        self::STATUT_VALIDATED,
        self::STATUT_SIGNED,
        self::STATUT_ARCHIVED,
        self::STATUT_CANCELLED,
    ];

    const LIBELLES_STATUT = [
        self::STATUT_DRAFT => 'Brouillon',
        self::STATUT_GENERATED => 'Généré',
        self::STATUT_REVIEW => 'En relecture',
        self::STATUT_VALIDATED => 'Validé',
        self::STATUT_SIGNED => 'Signé',
        self::STATUT_ARCHIVED => 'Archivé',
        self::STATUT_CANCELLED => 'Annulé',
    ];

    protected $fillable = [
        'reference',
        'type',
        'title',
        'document_template_id',
        'document_template_version_id',
        'documentable_type',
        'documentable_id',
        'content',
        'status',
        'signature_status',
        'signed_at',
        'context_snapshot',
        'generated_by_id',
        'generated_at',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'context_snapshot' => 'array',
        'generated_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function version()
    {
        return $this->belongsTo(DocumentTemplateVersion::class, 'document_template_version_id');
    }

    public function documentable()
    {
        return $this->morphTo();
    }

    public function revisions()
    {
        return $this->hasMany(DocumentRevision::class)->latest();
    }

    public function generePar()
    {
        return $this->belongsTo(User::class, 'generated_by_id');
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function libelleStatut(): string
    {
        return self::LIBELLES_STATUT[$this->status] ?? $this->status;
    }
}
