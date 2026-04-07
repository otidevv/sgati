<?php

namespace App\Enums;

enum ServerFunction: string
{
    case Production  = 'production';
    case Development = 'development';
    case Staging     = 'staging';
    case Database    = 'database';
    case Backup      = 'backup';
    case Testing     = 'testing';

    public function label(): string
    {
        return match($this) {
            self::Production  => 'Producción',
            self::Development => 'Desarrollo',
            self::Staging     => 'Staging',
            self::Database    => 'Base de Datos',
            self::Backup      => 'Respaldo',
            self::Testing     => 'Pruebas',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Production  => 'emerald',
            self::Development => 'blue',
            self::Staging     => 'amber',
            self::Database    => 'violet',
            self::Backup      => 'slate',
            self::Testing     => 'orange',
        };
    }
}
