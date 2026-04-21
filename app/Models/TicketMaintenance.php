<?php

namespace App\Models;

use App\Enums\CategorieTicket;
use App\Enums\PrioriteTicket;
use App\Enums\StatutTicket;
use Database\Factories\TicketMaintenanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['contrat_id', 'soumis_par_user_id', 'titre', 'categorie', 'priorite', 'description', 'statut'])]
class TicketMaintenance extends Model
{
    /** @use HasFactory<TicketMaintenanceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'categorie' => CategorieTicket::class,
            'priorite' => PrioriteTicket::class,
            'statut' => StatutTicket::class,
        ];
    }

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function soumisParUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'soumis_par_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MessageTicket::class);
    }

    public function piecesJointes(): MorphMany
    {
        return $this->morphMany(PieceJointe::class, 'attachable');
    }

    public function isActif(): bool
    {
        return $this->statut->isActif();
    }

    public function scopeActif($query)
    {
        return $query->whereIn('statut', [
            StatutTicket::Ouvert->value,
            StatutTicket::EnCours->value,
            StatutTicket::EnAttenteLocataire->value,
        ]);
    }
}
