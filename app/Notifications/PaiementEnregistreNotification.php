<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaiementEnregistreNotification extends Notification
{
    use Queueable;

    public function __construct(public Paiement $paiement) {}

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
            'type' => 'paiement_enregistre',
            'paiement_id' => $this->paiement->id,
            'montant' => (float) $this->paiement->montant,
            'periode' => $this->paiement->labelPeriode(),
            'bien_nom' => $this->paiement->contrat->bien->nom,
            'locataire_nom' => $this->paiement->contrat->locataire->nomComplet(),
            'message' => sprintf(
                '%s a enregistré un paiement de %s pour %s',
                $this->paiement->contrat->locataire->nomComplet(),
                number_format((float) $this->paiement->montant, 0, ',', ' ').' FCFA',
                $this->paiement->labelPeriode(),
            ),
            'url' => route('proprietaire.paiements.show', $this->paiement),
        ];
    }
}
