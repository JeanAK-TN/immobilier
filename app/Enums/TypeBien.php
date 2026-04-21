<?php

namespace App\Enums;

enum TypeBien: string
{
    case Maison = 'maison';
    case Appartement = 'appartement';
    case Terrain = 'terrain';
    case Bureau = 'bureau';
    case Commercial = 'commercial';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            TypeBien::Maison => 'Maison',
            TypeBien::Appartement => 'Appartement',
            TypeBien::Terrain => 'Terrain',
            TypeBien::Bureau => 'Bureau',
            TypeBien::Commercial => 'Local commercial',
            TypeBien::Autre => 'Autre',
        };
    }
}
