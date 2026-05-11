<?php

namespace App\Enums;

enum SystemDecommissionReason: string
{
    case Obsolete        = 'obsolete';
    case Replaced        = 'replaced';
    case ContractExpired = 'contract_expired';
    case Budget          = 'budget';
    case Merge           = 'merge';
    case Directive       = 'directive';
    case Other           = 'other';

    public function label(): string
    {
        return match($this) {
            self::Obsolete        => 'Tecnología obsoleta',
            self::Replaced        => 'Reemplazado por otro sistema',
            self::ContractExpired => 'Contrato/licencia vencida',
            self::Budget          => 'Recorte presupuestal',
            self::Merge           => 'Fusión con otro sistema',
            self::Directive       => 'Directiva institucional',
            self::Other           => 'Otro motivo',
        };
    }
}
