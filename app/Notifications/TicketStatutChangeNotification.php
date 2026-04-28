<?php

namespace App\Notifications;

use App\Enums\StatutTicket;
use App\Models\TicketMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatutChangeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TicketMaintenance $ticket,
        public StatutTicket $ancienStatut,
    ) {}

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
            'type' => 'ticket_statut_change',
            'ticket_id' => $this->ticket->id,
            'ticket_titre' => $this->ticket->titre,
            'ancien_statut' => $this->ancienStatut->value,
            'nouveau_statut' => $this->ticket->statut->value,
            'message' => sprintf(
                'Votre ticket « %s » est désormais : %s',
                $this->ticket->titre,
                $this->ticket->statut->label(),
            ),
            'url' => route('locataire.tickets.show', $this->ticket),
        ];
    }
}
