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
            ModePaiement::MobileMoney => 'Mobile Money (simulé)',
            ModePaiement::Virement => 'Virement bancaire (simulé)',
            ModePaiement::Especes => 'Espèces (simulé)',
            ModePaiement::Cheque => 'Chèque (simulé)',
            ModePaiement::Autre => 'Autre (simulé)',
        };
    }
}
