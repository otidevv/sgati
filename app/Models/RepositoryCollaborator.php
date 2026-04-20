<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepositoryCollaborator extends Model
{
    protected $fillable = [
        'repository_id', 'persona_id', 'role',
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

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public static function roleLabel(string $role): string
    {
        return match($role) {
            'owner'      => 'Propietario',
            'maintainer' => 'Mantenedor',
            'developer'  => 'Desarrollador',
            'reader'     => 'Lector',
            'deployer'   => 'Despliegue (CI/CD)',
            default      => $role,
        };
    }
}
