<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalActivite extends Model
{
    const UPDATED_AT = null;

    protected $table = 'journaux_activite';

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'entity_type',
        'entity_id',
        'donnees_avant',
        'donnees_apres',
        'motif',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Motif affiché dans le journal : le motif réel s'il a été renseigné, sinon
     * une phrase générée automatiquement à partir de l'action et de l'entité,
     * pour éviter d'afficher un simple tiret pour la majorité des entrées
     * (création/restauration n'ont jamais de motif explicite).
     */
    public function motifAffiche(): string
    {
        if (! empty($this->motif)) {
            return $this->motif;
        }

        return match ($this->action) {
            'creation' => "Création automatique de {$this->entity_type} #{$this->entity_id}",
            'modification' => "Modification de {$this->entity_type} #{$this->entity_id} sans motif renseigné",
            'suppression' => "Suppression de {$this->entity_type} #{$this->entity_id} sans motif renseigné",
            'restauration' => "Restauration de {$this->entity_type} #{$this->entity_id} depuis la corbeille",
            default => "Action {$this->action} sur {$this->entity_type} #{$this->entity_id}",
        };
    }
}
