<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatabaseServerResponsible extends Model
{
    protected $fillable = [
        'database_server_id', 'persona_id', 'level',
        'document_type', 'document_number', 'document_date', 'document_notes',
        'assigned_at', 'unassigned_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'document_date'  => 'date',
            'assigned_at'    => 'date',
            'unassigned_at'  => 'date',
            'is_active'      => 'boolean',
        ];
    }

    public function databaseServer()
    {
        return $this->belongsTo(DatabaseServer::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DatabaseServerResponsibleDocument::class)->orderBy('created_at');
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
