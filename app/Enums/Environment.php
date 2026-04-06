<?php

namespace App\Enums;

enum Environment: string
{
    case Production  = 'production';
    case Staging     = 'staging';
    case Development = 'development';

    public function label(): string
    {
        return match($this) {
            self::Production  => 'Producción',
            self::Staging     => 'Staging',
            self::Development => 'Desarrollo',
        };
    }
}
