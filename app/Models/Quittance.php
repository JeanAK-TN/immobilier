<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\QuittanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'contrat_id', 'paiement_id', 'generee_par_user_id',
    'periode_mois', 'periode_annee', 'numero_quittance',
    'emise_le', 'fichier_path',
])]
class Quittance extends Model
{
    /** @use HasFactory<QuittanceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'emise_le' => 'datetime',
        ];
    }

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    public function genereeParUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generee_par_user_id');
    }

    public function labelPeriode(): string
    {
        $mois = Carbon::createFromDate($this->periode_annee, $this->periode_mois, 1)->translatedFormat('F Y');

        return ucfirst($mois);
    }

    public static function genererNumero(): string
    {
        $annee = now()->year;
        $sequence = static::whereYear('created_at', $annee)->count() + 1;

        return sprintf('QUIT-%s-%04d', $annee, $sequence);
    }

    public function documentDisponible(): bool
    {
        return filled($this->fichier_path);
    }

    public function nomFichier(): string
    {
        return sprintf('quittance-%s.pdf', strtolower($this->numero_quittance));
    }

    public function scopePourProprietaire(Builder $query, User $user): Builder
    {
        return $query->whereHas('contrat.bien', function (Builder $builder) use ($user): void {
            $builder->whereBelongsTo($user, 'proprietaire');
        });
    }

    public function scopePourLocataire(Builder $query, User $user): Builder
    {
        return $query->whereHas('contrat.locataire', function (Builder $builder) use ($user): void {
            $builder->where('user_id', $user->id);
        });
    }
}
