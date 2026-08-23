<?php

namespace App\Models\Documents;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use SoftDeletes;
    use Auditable;

    /**
     * Module utilisé par le trait Auditable pour alimenter le journal d'activité partagé
     * (visible dans /locative/journal aux côtés des autres modules).
     */
    protected string $moduleJournal = 'documents';

    const STATUT_DRAFT = 'draft';
    const STATUT_ACTIVE = 'active';
    const STATUT_INACTIVE = 'inactive';
    const STATUT_ARCHIVED = 'archived';

    const STATUTS = [self::STATUT_DRAFT, self::STATUT_ACTIVE, self::STATUT_INACTIVE, self::STATUT_ARCHIVED];

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'status',
        'created_by_id',
        'supprime_par_id',
        'motif_suppression',
    ];

    public function versions()
    {
        return $this->hasMany(DocumentTemplateVersion::class)->orderByDesc('version');
    }

    public function activeVersion()
    {
        return $this->hasOne(DocumentTemplateVersion::class)->where('status', DocumentTemplateVersion::STATUT_ACTIVE);
    }

    public function latestDraftVersion()
    {
        return $this->hasOne(DocumentTemplateVersion::class)->where('status', DocumentTemplateVersion::STATUT_DRAFT)->latestOfMany('version');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function prochainNumeroVersion(): int
    {
        return (int) $this->versions()->max('version') + 1;
    }
}
