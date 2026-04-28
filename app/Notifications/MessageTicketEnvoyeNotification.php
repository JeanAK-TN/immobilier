<?php

namespace App\Notifications;

use App\Models\TicketMaintenance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MessageTicketEnvoyeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TicketMaintenance $ticket,
        public User $auteur,
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
        $url = $notifiable->isProprietaire()
            ? route('proprietaire.tickets.show', $this->ticket)
            : route('locataire.tickets.show', $this->ticket);

        return [
            'type' => 'message_ticket',
            'ticket_id' => $this->ticket->id,
            'ticket_titre' => $this->ticket->titre,
            'auteur_nom' => $this->auteur->name,
            'message' => sprintf(
                '%s a répondu sur le ticket « %s »',
                $this->auteur->name,
                $this->ticket->titre,
            ),
            'url' => $url,
        ];
    }
}
