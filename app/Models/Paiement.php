<?php

namespace App\Models;

use App\Enums\ModePaiement;
use App\Enums\OperateurMobileMoney;
use App\Enums\StatutPaiement;
use Carbon\Carbon;
use Database\Factories\PaiementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable(['contrat_id', 'periode_mois', 'periode_annee', 'montant', 'mode', 'operateur_mobile_money', 'reference', 'statut', 'notes'])]
class Paiement extends Model
{
    /** @use HasFactory<PaiementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'mode' => ModePaiement::class,
            'operateur_mobile_money' => OperateurMobileMoney::class,
            'statut' => StatutPaiement::class,
            'montant' => 'decimal:2',
        ];
    }

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function quittance(): HasOne
    {
        return $this->hasOne(Quittance::class);
    }

    public function isReussi(): bool
    {
        return $this->statut === StatutPaiement::SimuleReussi;
    }

    public function labelPeriode(): string
    {
        $mois = Carbon::createFromDate($this->periode_annee, $this->periode_mois, 1)->translatedFormat('F Y');

        return ucfirst($mois);
    }

    public function modeLabel(): string
    {
        if ($this->mode !== ModePaiement::MobileMoney) {
            return $this->mode->label();
        }

        $operateur = $this->operateur_mobile_money?->label();

        return $operateur
            ? sprintf('%s — %s', $this->mode->label(), $operateur)
            : $this->mode->label();
    }

    public static function genererReference(): string
    {
        do {
            $reference = sprintf(
                'SIM-TG-%s-%s',
                now()->format('Ym'),
                Str::upper(Str::random(6))
            );
        } while (static::query()->where('reference', $reference)->exists());

        return $reference;
    }

    public function scopeReussi(Builder $query): Builder
    {
        return $query->where('statut', StatutPaiement::SimuleReussi);
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
