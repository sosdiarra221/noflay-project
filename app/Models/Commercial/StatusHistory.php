<?php

namespace App\Models\Commercial;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    const UPDATED_AT = null;

    protected $table = 'commercial_status_histories';

    protected $fillable = [
        'prospect_id',
        'ancien_statut',
        'nouveau_statut',
        'commentaire',
        'user_id',
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
