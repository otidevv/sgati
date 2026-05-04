<?php

namespace App\Enums;

enum SystemOriginType: string
{
    case Donated     = 'donated';
    case ThirdParty  = 'third_party';
    case Internal    = 'internal';
    case State       = 'state';

    public function label(): string
    {
        return match($this) {
            self::Donated    => 'Donado',
            self::ThirdParty => 'Creado por terceros',
            self::Internal   => 'Desarrollo interno',
            self::State      => 'Sistema del Estado Peruano',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Donated    => 'Recibido como donación (tesis, convenio, etc.)',
            self::ThirdParty => 'Desarrollado por empresa o proveedor externo',
            self::Internal   => 'Desarrollado por la propia oficina/equipo',
            self::State      => 'Provisto por una entidad del Estado Peruano',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Donated    => 'purple',
            self::ThirdParty => 'orange',
            self::Internal   => 'teal',
            self::State      => 'red',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Donated    => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7',
            self::ThirdParty => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            self::Internal   => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            self::State      => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
        };
    }
}
