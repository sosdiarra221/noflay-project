<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglageEditeur extends Model
{
    protected $table = 'reglages_editeur';

    protected $connection = 'central';

    protected $fillable = [
        'nom_application',
        'logo',
        'favicon',
        'smtp_type',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'pusher_app_id',
        'pusher_key',
        'pusher_secret',
        'pusher_cluster',
        'firebase_api_key',
        'firebase_project_id',
        'firebase_messaging_sender_id',
        'firebase_app_id',
    ];

    protected $casts = [
        'smtp_password' => 'encrypted',
        'pusher_secret' => 'encrypted',
    ];

    public static function courant(): self
    {
        return static::first() ?? static::create([
            'nom_application' => config('app.name'),
            'smtp_type' => 'log',
        ]);
    }
}
