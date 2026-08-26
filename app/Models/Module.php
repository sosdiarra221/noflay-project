<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Module extends Model
{
    protected $fillable = [
        'cle',
        'nom',
        'description',
        'icone',
        'actif',
        'ordre',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Un module est actif pour la société courante si l'éditeur du logiciel le lui a
     * explicitement accordé (table centrale `tenant_modules`, gérée depuis l'espace
     * "Administrateur du logiciel"). "administration" est toujours actif : une PME doit
     * pouvoir gérer ses utilisateurs même si un autre module lui est retiré. Hors contexte
     * tenant (CLI, domaine central), on reste en fail-open — comportement inchangé.
     */
    public static function estActif(string $cle): bool
    {
        if ($cle === 'administration') {
            return true;
        }

        if (! function_exists('tenant') || ! tenant()) {
            return true;
        }

        return (bool) DB::connection('central')
            ->table('tenant_modules')
            ->where('tenant_id', tenant('id'))
            ->where('module_cle', $cle)
            ->value('actif');
    }
}
