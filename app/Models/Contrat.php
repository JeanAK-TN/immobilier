<?php

namespace App\Models;

use App\Enums\StatutContrat;
use Database\Factories\ContratFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'bien_id', 'locataire_id', 'date_debut', 'date_fin', 'reconduction_auto',
    'loyer_mensuel', 'depot_garantie', 'charges', 'jour_paiement',
    'statut', 'document_path', 'signe_le', 'signe_nom', 'signe_ip',
])]
class Contrat extends Model
{
    /** @use HasFactory<ContratFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'signe_le' => 'datetime',
            'reconduction_auto' => 'boolean',
            'loyer_mensuel' => 'decimal:2',
            'depot_garantie' => 'decimal:2',
            'charges' => 'decimal:2',
            'statut' => StatutContrat::class,
        ];
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    public function locataire(): BelongsTo
    {
        return $this->belongsTo(Locataire::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function quittances(): HasMany
    {
        return $this->hasMany(Quittance::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TicketMaintenance::class);
    }

    public function isActif(): bool
    {
        return $this->statut === StatutContrat::Actif;
    }

    public function isSigne(): bool
    {
        return $this->signe_le !== null;
    }

    public function montantTotalMensuel(): float
    {
        return (float) $this->loyer_mensuel + (float) $this->charges;
    }

    public function scopeActif($query)
    {
        return $query->where('statut', StatutContrat::Actif);
    }
}
