<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContratAttribueNotification extends Notification
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
        $aSigner = $this->contrat->peutEtreSigne();

        return [
            'type' => 'contrat_attribue',
            'contrat_id' => $this->contrat->id,
            'bien_nom' => $this->contrat->bien->nom,
            'a_signer' => $aSigner,
            'message' => $aSigner
                ? sprintf('Un nouveau bail vous attend : %s. Pensez à le signer.', $this->contrat->bien->nom)
                : sprintf('Un nouveau contrat vous a été attribué : %s.', $this->contrat->bien->nom),
            'url' => route('locataire.contrat.show'),
        ];
    }
}
