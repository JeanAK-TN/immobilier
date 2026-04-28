<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContratSigneNotification extends Notification
{
    use Queueable;

    public function __construct(public Contrat $contrat) {}

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
            'type' => 'contrat_signe',
            'contrat_id' => $this->contrat->id,
            'bien_nom' => $this->contrat->bien->nom,
            'locataire_nom' => $this->contrat->locataire->nomComplet(),
            'message' => sprintf(
                '%s a signé le bail de %s',
                $this->contrat->locataire->nomComplet(),
                $this->contrat->bien->nom,
            ),
            'url' => route('proprietaire.contrats.show', $this->contrat),
        ];
    }
}
