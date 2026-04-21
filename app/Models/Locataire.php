<?php

namespace App\Models;

use Database\Factories\LocataireFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
}
