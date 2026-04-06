<?php

namespace App\Enums;

enum DocType: string
{
    case ManualUser      = 'manual_user';
    case ManualTechnical = 'manual_technical';
    case Oficio          = 'oficio';
    case Resolution      = 'resolution';
    case Acta            = 'acta';
    case Contract        = 'contract';
    case Diagram         = 'diagram';
    case Other           = 'other';

    public function label(): string
    {
        return match($this) {
            self::ManualUser      => 'Manual de usuario',
            self::ManualTechnical => 'Manual técnico',
            self::Oficio          => 'Oficio',
            self::Resolution      => 'Resolución',
            self::Acta            => 'Acta',
            self::Contract        => 'Contrato',
            self::Diagram         => 'Diagrama',
            self::Other           => 'Otro',
        };
    }
}
