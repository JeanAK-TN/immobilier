<?php

namespace App\Enums;

enum CategorieTicket: string
{
    case Plomberie = 'plomberie';
    case Electricite = 'electricite';
    case Chauffage = 'chauffage';
    case Menuiserie = 'menuiserie';
    case Serrurerie = 'serrurerie';
    case Peinture = 'peinture';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            CategorieTicket::Plomberie => 'Plomberie',
            CategorieTicket::Electricite => 'Électricité',
            CategorieTicket::Chauffage => 'Chauffage',
            CategorieTicket::Menuiserie => 'Menuiserie',
            CategorieTicket::Serrurerie => 'Serrurerie',
            CategorieTicket::Peinture => 'Peinture',
            CategorieTicket::Autre => 'Autre',
        };
    }
}
