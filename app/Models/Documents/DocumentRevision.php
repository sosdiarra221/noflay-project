<?php

namespace App\Models\Documents;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Historique d'un Document généré. C'est CE modèle qui sert de journal détaillé (contenu avant/après)
 * pour le module Documents — il n'utilise pas le trait Auditable pour éviter de journaliser le journal.
 */
class DocumentRevision extends Model
{
    const ACTION_CREATION = 'creation';
    const ACTION_EDITION = 'edition';
    const ACTION_REGENERATION = 'regeneration';
    const ACTION_CHANGEMENT_STATUT = 'changement_statut';

    protected $fillable = [
        'document_id',
        'action',
        'content_before',
        'content_after',
        'changes',
        'user_id',
        'note',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libelleAction(): string
    {
        return match ($this->action) {
            self::ACTION_CREATION => 'Création',
            self::ACTION_EDITION => 'Modification',
            self::ACTION_REGENERATION => 'Régénération',
            self::ACTION_CHANGEMENT_STATUT => 'Changement de statut',
            default => $this->action,
        };
    }
}
