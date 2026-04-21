<?php

namespace App\Models;

use App\Enums\CategorieTicket;
use App\Enums\PrioriteTicket;
use App\Enums\StatutTicket;
use Database\Factories\TicketMaintenanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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

    public function peutRecevoirMessage(): bool
    {
        return $this->statut !== StatutTicket::Ferme;
    }

    public function scopeActif(Builder $query): Builder
    {
        return $query->whereIn('statut', [
            StatutTicket::Ouvert->value,
            StatutTicket::EnCours->value,
            StatutTicket::EnAttenteLocataire->value,
        ]);
    }

    public function scopePourProprietaire(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'contrat.bien',
            fn (Builder $builder) => $builder->whereBelongsTo($user, 'proprietaire')
        );
    }

    public function scopePourLocataire(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'contrat.locataire',
            fn (Builder $builder) => $builder->where('user_id', $user->id)
        );
    }

    public function scopeRecherche(Builder $query, ?string $terme): Builder
    {
        $terme = trim((string) $terme);

        if ($terme === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($terme): void {
            $builder
                ->where('titre', 'like', "%{$terme}%")
                ->orWhere('description', 'like', "%{$terme}%")
                ->orWhereHas('contrat.bien', function (Builder $bienQuery) use ($terme): void {
                    $bienQuery
                        ->where('nom', 'like', "%{$terme}%")
                        ->orWhere('adresse', 'like', "%{$terme}%")
                        ->orWhere('ville', 'like', "%{$terme}%");
                })
                ->orWhereHas('contrat.locataire', function (Builder $locataireQuery) use ($terme): void {
                    $locataireQuery
                        ->where('prenom', 'like', "%{$terme}%")
                        ->orWhere('nom', 'like', "%{$terme}%")
                        ->orWhere('email', 'like', "%{$terme}%");
                });
        });
    }
}
