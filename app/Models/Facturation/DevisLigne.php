<?php

namespace App\Models\Facturation;

use Illuminate\Database\Eloquent\Model;

class DevisLigne extends Model
{
    protected $table = 'devis_lignes';

    protected $fillable = [
        'devis_id',
        'designation',
        'quantite',
        'prix_unitaire',
        'total',
        'ordre',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }
}
