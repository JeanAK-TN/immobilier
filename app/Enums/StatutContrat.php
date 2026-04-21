<?php

namespace App\Enums;

enum StatutContrat: string
{
    case Brouillon = 'brouillon';
    case EnAttente = 'en_attente_signature';
    case Actif = 'actif';
    case Termine = 'termine';
    case Resilie = 'resilie';

    public function label(): string
    {
        return match ($this) {
            StatutContrat::Brouillon => 'Brouillon',
            StatutContrat::EnAttente => 'En attente de signature',
            StatutContrat::Actif => 'Actif',
            StatutContrat::Termine => 'Terminé',
            StatutContrat::Resilie => 'Résilié',
        };
    }

    public function isActif(): bool
    {
        return $this === StatutContrat::Actif;
    }
}
