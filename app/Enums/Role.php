<?php

namespace App\Enums;

enum Role: string
{
    case Proprietaire = 'proprietaire';
    case Locataire = 'locataire';

    public function label(): string
    {
        return match ($this) {
            Role::Proprietaire => 'Propriétaire',
            Role::Locataire => 'Locataire',
        };
    }
}
