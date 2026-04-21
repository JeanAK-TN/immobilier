<?php

namespace App\Models;

use Database\Factories\MessageTicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ticket_maintenance_id', 'user_id', 'message', 'est_note_interne'])]
class MessageTicket extends Model
{
    /** @use HasFactory<MessageTicketFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'est_note_interne' => 'boolean',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketMaintenance::class, 'ticket_maintenance_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estVisiblePour(User $user): bool
    {
        return $user->isProprietaire() || ! $this->est_note_interne;
    }
}
