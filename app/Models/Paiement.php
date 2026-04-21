<?php

namespace App\Models;

use App\Enums\ModePaiement;
use App\Enums\StatutPaiement;
use Carbon\Carbon;
use Database\Factories\PaiementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['contrat_id', 'periode_mois', 'periode_annee', 'montant', 'mode', 'reference', 'statut', 'notes'])]
class Paiement extends Model
{
    /** @use HasFactory<PaiementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'mode' => ModePaiement::class,
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

    public function scopeReussi($query)
    {
        return $query->where('statut', StatutPaiement::SimuleReussi);
    }
}
