<?php

namespace App\Models\Commercial;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use Auditable;

    protected $table = 'commercial_rendez_vous';

    protected string $moduleJournal = 'commercial';

    protected $fillable = [
        'prospect_id',
        'titre',
        'type',
        'date_debut',
        'date_fin',
        'lieu',
        'description',
        'statut',
        'user_id',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
