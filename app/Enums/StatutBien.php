<?php

namespace App\Enums;

enum StatutBien: string
{
    case Disponible = 'disponible';
    case Occupe = 'occupe';

    public function label(): string
    {
        return match ($this) {
            StatutBien::Disponible => 'Disponible',
            StatutBien::Occupe => 'Occupé',
        };
    }
}
