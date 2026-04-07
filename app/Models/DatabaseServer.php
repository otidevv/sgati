<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseServer extends Model
{
    protected $fillable = [
        'server_id', 'engine', 'version',
        'host', 'port',
        'admin_user', 'admin_password',
        'name', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'admin_password' => 'encrypted',
            'is_active'      => 'boolean',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────

    /**
     * Servidor físico donde corre este motor.
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Bases de datos alojadas en este motor.
     */
    public function databases()
    {
        return $this->hasMany(SystemDatabase::class);
    }

    /**
     * Sistemas que usan este motor (a través de sus BDs).
     */
    public function systems()
    {
        return $this->hasManyThrough(
            System::class,
            SystemDatabase::class,
            'database_server_id', // FK en system_databases
            'id',                 // PK en systems
            'id',                 // PK en database_servers
            'system_id',          // FK en system_databases
        );
    }

    // ── Accessors ─────────────────────────────────────────────────────

    /**
     * Conexión legible: "192.168.254.5:5432"
     */
    public function getConnectionStringAttribute(): string
    {
        return ($this->host ?? 'localhost') . ':' . ($this->port ?? '?');
    }

    /**
     * Etiqueta del motor con versión: "PostgreSQL 16.2"
     */
    public function getEngineLabelAttribute(): string
    {
        $engine = ucfirst($this->engine);
        return $this->version ? "{$engine} {$this->version}" : $engine;
    }
}
