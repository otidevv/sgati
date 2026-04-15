<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'area_id',
        'persona_id',
        'is_active',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'is_active'             => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function systems()
    {
        return $this->hasMany(System::class, 'responsible_id');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) return true;

        return $this->role?->hasPermission($permission) ?? false;
    }

    public function can($abilities, $arguments = []): bool
    {
        // Permite usar $user->can('systems.create') sin Gates registrados
        if (is_string($abilities)) {
            return $this->hasPermission($abilities);
        }

        return parent::can($abilities, $arguments);
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->persona
            ? $this->persona->nombre_completo
            : $this->name;
    }
}
