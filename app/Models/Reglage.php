<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reglage extends Model
{
    protected $fillable = [
        'nom_societe',
        'logo',
        'email',
        'telephone',
        'adresse',
        'site_web',
        'devise_id',
        'smtp_type',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
    ];

    protected $casts = [
        'smtp_password' => 'encrypted',
    ];

    public function devise()
    {
        return $this->belongsTo(Devise::class);
    }

    public static function courant(): self
    {
        return static::first() ?? static::create([
            'nom_societe' => config('app.name'),
            'smtp_type' => 'log',
        ]);
    }
}
