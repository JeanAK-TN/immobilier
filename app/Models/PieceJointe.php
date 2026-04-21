<?php

namespace App\Models;

use Database\Factories\PieceJointeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['attachable_type', 'attachable_id', 'uploade_par_user_id', 'nom_fichier', 'nom_original', 'chemin', 'type_mime', 'taille'])]
class PieceJointe extends Model
{
    /** @use HasFactory<PieceJointeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'taille' => 'integer',
        ];
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploade_par_user_id');
    }

    public function url(): string
    {
        return Storage::url($this->chemin);
    }

    public function tailleFormatee(): string
    {
        $ko = $this->taille / 1024;
        if ($ko < 1024) {
            return round($ko, 1).' Ko';
        }

        return round($ko / 1024, 1).' Mo';
    }
}
