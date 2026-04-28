<?php

namespace App\Notifications;

use App\Models\Quittance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuittanceGenereeNotification extends Notification
{
    use Queueable;

    public function __construct(public Quittance $quittance) {}

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
            'type' => 'quittance_generee',
            'quittance_id' => $this->quittance->id,
            'numero' => $this->quittance->numero_quittance,
            'periode' => $this->quittance->labelPeriode(),
            'message' => sprintf(
                'Une nouvelle quittance est disponible : %s (%s).',
                $this->quittance->numero_quittance,
                $this->quittance->labelPeriode(),
            ),
            'url' => route('locataire.quittances.index'),
        ];
    }
}
