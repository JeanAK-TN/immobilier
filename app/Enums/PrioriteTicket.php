<?php

namespace App\Enums;

enum PrioriteTicket: string
{
    case Basse = 'basse';
    case Moyenne = 'moyenne';
    case Haute = 'haute';

    public function label(): string
    {
        return match ($this) {
            PrioriteTicket::Basse => 'Basse',
            PrioriteTicket::Moyenne => 'Moyenne',
            PrioriteTicket::Haute => 'Haute',
        };
    }
}
