<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'action', 'modele_type', 'modele_id', 'adresse_ip', 'user_agent', 'details'])]
class JournalAudit extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function enregistrer(string $action, ?Model $modele = null, array $details = []): static
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'modele_type' => $modele ? $modele::class : null,
            'modele_id' => $modele?->getKey(),
            'adresse_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details ?: null,
        ]);
    }
}
