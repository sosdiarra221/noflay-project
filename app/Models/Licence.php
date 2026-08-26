<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Licence extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'tenant_id',
        'package_id',
        'date_debut',
        'date_fin',
        'genere_par_admin_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function genereParAdmin()
    {
        return $this->belongsTo(Admin::class, 'genere_par_admin_id');
    }

    public function estExpiree(): bool
    {
        return $this->date_fin->lt(Carbon::today());
    }

    public function joursRestants(): int
    {
        return (int) Carbon::today()->diffInDays($this->date_fin, false);
    }
}
