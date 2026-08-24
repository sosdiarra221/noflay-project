<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'identifiant',
        'password',
        'code_pin',
        'photo',
        'statut',
        'role_id',
        'departement_id',
        'derniere_activite_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'code_pin',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'code_pin' => 'hashed',
            'derniere_activite_at' => 'datetime',
        ];
    }

    public function avatarUrl(): string
    {
        return $this->photo ? asset('storage/'.$this->photo) : asset('assets/images/avatar/avatar-10.jpg');
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function prospectsGeres()
    {
        return $this->hasMany(\App\Models\Commercial\Prospect::class, 'commercial_id');
    }

    public function aLeRole(string ...$noms): bool
    {
        return $this->role && in_array($this->role->nom, $noms, true);
    }
}
