<?php

namespace App\Models\Concerns;

use App\Models\JournalActivite;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Auditable
{
    /**
     * Motif optionnel à associer à la PROCHAINE modification, pour les opérations sensibles.
     * À renseigner avant `save()`/`update()` ; consommé et remis à null automatiquement.
     */
    public ?string $motifAction = null;

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->journaliser('creation', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changements = $model->getChanges();
            unset($changements['updated_at']);

            if (empty($changements) || array_keys($changements) === ['deleted_at']) {
                return;
            }

            $avant = [];
            foreach (array_keys($changements) as $colonne) {
                $avant[$colonne] = $model->getOriginal($colonne);
            }

            $motif = $model->motifAction ?? null;
            $model->motifAction = null;

            $model->journaliser('modification', $avant, $changements, $motif);
        });

        static::deleted(function ($model) {
            $model->journaliser('suppression', $model->getOriginal(), null, $model->motif_suppression ?? null);
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function ($model) {
                $model->journaliser('restauration', ['deleted_at' => $model->getRawOriginal('deleted_at')], ['deleted_at' => null]);
            });
        }
    }

    protected function journaliser(string $action, ?array $avant, ?array $apres, ?string $motif = null): void
    {
        JournalActivite::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => property_exists($this, 'moduleJournal') ? $this->moduleJournal : 'locative',
            'entity_type' => class_basename($this),
            'entity_id' => $this->getKey(),
            'donnees_avant' => $avant,
            'donnees_apres' => $apres,
            'motif' => $motif,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
