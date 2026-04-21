<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'must_change_password', 'is_active'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function isProprietaire(): bool
    {
        return $this->role === Role::Proprietaire;
    }

    public function isLocataire(): bool
    {
        return $this->role === Role::Locataire;
    }

    public function biens(): HasMany
    {
        return $this->hasMany(Bien::class);
    }

    public function locataire(): HasOne
    {
        return $this->hasOne(Locataire::class);
    }

    public function locatairesGeres(): HasMany
    {
        return $this->hasMany(Locataire::class, 'cree_par_user_id');
    }
}
