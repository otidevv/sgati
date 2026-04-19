<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemVersionResponsible extends Model
{
    protected $fillable = [
        'system_version_id', 'persona_id', 'role',
        'document_notes',
        'assigned_at', 'unassigned_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at'   => 'date',
            'unassigned_at' => 'date',
            'is_active'     => 'boolean',
        ];
    }

    public function systemVersion()
    {
        return $this->belongsTo(SystemVersion::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SystemVersionResponsibleDocument::class)->orderBy('created_at');
    }

    public static function roleLabel(string $role): string
    {
        return match ($role) {
            'lider_tecnico' => 'Líder Técnico',
            'desarrollador' => 'Desarrollador',
            'analista'      => 'Analista',
            'tester'        => 'Tester / QA',
            'despliegue'    => 'Responsable de Despliegue',
            'aprobador'     => 'Aprobador',
            default         => $role,
        };
    }
}
