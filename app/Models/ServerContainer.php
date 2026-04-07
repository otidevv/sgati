<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerContainer extends Model
{
    protected $table = 'server_containers';

    protected $fillable = [
        'server_id', 'system_id',
        'name', 'image', 'type',
        'internal_port', 'external_port',
        'env_vars', 'volumes',
        'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'env_vars'  => 'array',
            'volumes'   => 'array',
            'is_active' => 'boolean',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Accessors ─────────────────────────────────────────────────────

    /**
     * Mapeo de puertos legible: "3000:8000"
     */
    public function getPortMappingAttribute(): ?string
    {
        if (!$this->external_port && !$this->internal_port) return null;
        return ($this->external_port ?? '?') . ':' . ($this->internal_port ?? '?');
    }
}
