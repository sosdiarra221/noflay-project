<?php

namespace App\Models\Commercial;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    protected $table = 'commercial_activites';

    protected $fillable = [
        'prospect_id',
        'type',
        'objet',
        'description',
        'date_activite',
        'user_id',
    ];

    protected $casts = [
        'date_activite' => 'datetime',
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
