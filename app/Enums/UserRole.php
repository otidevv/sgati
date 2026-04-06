<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin      = 'admin';
    case Technician = 'technician';
    case Documenter = 'documenter';
    case Viewer     = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::Admin      => 'Administrador',
            self::Technician => 'Técnico',
            self::Documenter => 'Documentador',
            self::Viewer     => 'Visualizador',
        };
    }
}
