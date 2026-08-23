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
}
