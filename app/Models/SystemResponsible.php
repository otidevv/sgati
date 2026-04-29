<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemResponsible extends Model
{
    use LogsSystemActivity;
    protected $fillable = [
        'system_id', 'persona_id', 'level',
        'document_notes',
        'assigned_at', 'unassigned_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at'   => 'date',
            'unassigned_at' => 'date',
            'is_active'     => 'boolean',
            'level'         => 'array',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SystemResponsibleDocument::class)->orderBy('created_at');
    }

    protected function activitySubjectType(): string { return 'responsable'; }

    public static function levelLabel(string $level): string
    {
        return match($level) {
            'lider_proyecto' => 'Líder de Proyecto',
            'desarrollador'  => 'Desarrollador',
            'mantenimiento'  => 'Mantenimiento',
            'administrador'  => 'Administrador',
            'analista'       => 'Analista',
            'soporte'        => 'Soporte Técnico',
            'supervision'    => 'Supervisión',
            default          => $level,
        };
    }
}
