<?php

namespace App\Enums;

enum OperateurMobileMoney: string
{
    case MixxTMoney = 'mixx_tmoney';
    case Moov = 'moov';

    public function label(): string
    {
        return match ($this) {
            OperateurMobileMoney::MixxTMoney => 'Mixx/TMoney',
            OperateurMobileMoney::Moov => 'Moov',
        };
    }
}
