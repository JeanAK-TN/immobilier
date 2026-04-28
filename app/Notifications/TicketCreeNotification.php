<?php

namespace App\Notifications;

use App\Models\TicketMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketCreeNotification extends Notification
{
    use Queueable;

    public function __construct(public TicketMaintenance $ticket) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_cree',
            'ticket_id' => $this->ticket->id,
            'titre' => $this->ticket->titre,
            'priorite' => $this->ticket->priorite->value,
            'bien_nom' => $this->ticket->contrat->bien->nom,
            'locataire_nom' => $this->ticket->contrat->locataire->nomComplet(),
            'message' => sprintf(
                '%s a ouvert un ticket : %s',
                $this->ticket->contrat->locataire->nomComplet(),
                $this->ticket->titre,
            ),
            'url' => route('proprietaire.tickets.show', $this->ticket),
        ];
    }
}
