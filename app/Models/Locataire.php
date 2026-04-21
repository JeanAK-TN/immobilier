<?php

namespace App\Models;

use Database\Factories\LocataireFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'cree_par_user_id', 'prenom', 'nom', 'telephone', 'email', 'piece_identite_path'])]
class Locataire extends Model
{
    /** @use HasFactory<LocataireFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par_user_id');
    }

    public function contrats(): HasMany
    {
        return $this->hasMany(Contrat::class);
    }

    public function nomComplet(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function compteActif(): bool
    {
        return $this->user?->is_active ?? false;
    }

    public function scopePourProprietaire(Builder $query, User $user): Builder
    {
        return $query->whereBelongsTo($user, 'creePar');
    }

    public function scopeRecherche(Builder $query, ?string $terme): Builder
    {
        $terme = trim((string) $terme);

        if ($terme === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($terme): void {
            $builder
                ->where('prenom', 'like', "%{$terme}%")
                ->orWhere('nom', 'like', "%{$terme}%")
                ->orWhere('email', 'like', "%{$terme}%")
                ->orWhere('telephone', 'like', "%{$terme}%");
        });
    }
}
