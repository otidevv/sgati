<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServerResponsible extends Model
{
    protected $fillable = [
        'server_id', 'persona_id', 'level',
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

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ServerResponsibleDocument::class)->orderBy('created_at');
    }

    public static function levelLabel(string $level): string
    {
        return match($level) {
            'principal'   => 'Responsable Principal',
            'soporte'     => 'Soporte Técnico',
            'supervision' => 'Supervisión',
            'operador'    => 'Operador',
            default       => $level,
        };
    }
}
