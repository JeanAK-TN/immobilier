<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case SimuleReussi = 'simule_reussi';
    case SimuleEchec = 'simule_echec';

    public function label(): string
    {
        return match ($this) {
            StatutPaiement::SimuleReussi => 'Simulé - Réussi',
            StatutPaiement::SimuleEchec => 'Simulé - Échec',
        };
    }

    public function isReussi(): bool
    {
        return $this === StatutPaiement::SimuleReussi;
    }
}
