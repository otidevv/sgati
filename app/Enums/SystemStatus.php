<?php

namespace App\Enums;

enum SystemStatus: string
{
    case Active         = 'active';
    case Inactive       = 'inactive';
    case Development    = 'development';
    case Maintenance    = 'maintenance';
    case Decommissioned = 'decommissioned';

    public function label(): string
    {
        return match($this) {
            self::Active         => 'Activo',
            self::Inactive       => 'Inactivo',
            self::Development    => 'En desarrollo',
            self::Maintenance    => 'En mantenimiento',
            self::Decommissioned => 'Dado de baja',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Active         => 'green',
            self::Inactive       => 'red',
            self::Development    => 'blue',
            self::Maintenance    => 'yellow',
            self::Decommissioned => 'slate',
        };
    }
}
