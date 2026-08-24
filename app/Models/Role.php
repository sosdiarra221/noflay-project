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

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function estSysteme(): bool
    {
        return in_array($this->nom, [
            self::ADMINISTRATEUR,
            self::DIRECTEUR,
            self::AGENT_IMMOBILIER,
            self::COMPTABLE,
            self::ASSISTANT,
        ], true);
    }
}
