<?php

namespace App\Models\Documents;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplateVersion extends Model
{
    use Auditable;

    protected string $moduleJournal = 'documents';

    const STATUT_DRAFT = 'draft';
    const STATUT_ACTIVE = 'active';
    const STATUT_INACTIVE = 'inactive';
    const STATUT_ARCHIVED = 'archived';

    const STATUTS = [self::STATUT_DRAFT, self::STATUT_ACTIVE, self::STATUT_INACTIVE, self::STATUT_ARCHIVED];

    protected $fillable = [
        'document_template_id',
        'version',
        'status',
        'content',
        'notes',
        'created_by_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_template_version_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function estBrouillon(): bool
    {
        return $this->status === self::STATUT_DRAFT;
    }

    public function estActive(): bool
    {
        return $this->status === self::STATUT_ACTIVE;
    }
}
