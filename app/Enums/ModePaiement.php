<?php

namespace App\Enums;

enum ModePaiement: string
{
    case MobileMoney = 'mobile_money';
    case Virement = 'virement';
    case Especes = 'especes';
    case Cheque = 'cheque';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            ModePaiement::MobileMoney => 'Mobile Money',
            ModePaiement::Virement => 'Virement bancaire',
            ModePaiement::Especes => 'Espèces',
            ModePaiement::Cheque => 'Chèque',
            ModePaiement::Autre => 'Autre',
        };
    }
}
