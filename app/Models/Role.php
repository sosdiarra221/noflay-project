<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMINISTRATEUR = 'administrateur';
    const DIRECTEUR = 'directeur';
    const AGENT_IMMOBILIER = 'agent-immobilier';
    const COMPTABLE = 'comptable';
    const ASSISTANT = 'assistant';

    protected $fillable = [
        'nom',
        'libelle',
    ];

    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }
}
