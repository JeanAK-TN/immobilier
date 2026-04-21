<?php

namespace App\Models;

use App\Enums\StatutBien;
use App\Enums\TypeBien;
use Database\Factories\BienFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['user_id', 'nom', 'type', 'adresse', 'ville', 'pays', 'description', 'statut'])]
class Bien extends Model
{
    /** @use HasFactory<BienFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => TypeBien::class,
            'statut' => StatutBien::class,
        ];
    }

    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contrats(): HasMany
    {
        return $this->hasMany(Contrat::class);
    }

    public function contratActif(): HasMany
    {
        return $this->hasMany(Contrat::class)->where('statut', 'actif');
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(PieceJointe::class, 'attachable');
    }

    public function isOccupe(): bool
    {
        return $this->statut === StatutBien::Occupe;
    }
}
