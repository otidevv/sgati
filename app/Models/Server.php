<?php

namespace App\Models;

use App\Enums\ServerFunction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Server extends Model
{
    protected $fillable = [
        'name', 'slug',
        'ssh_user', 'ssh_password',
        'operating_system', 'function', 'host_type',
        'cpu_cores', 'ram_gb', 'storage_gb',
        'cloud_provider', 'cloud_region', 'cloud_instance',
        'installed_services', 'web_root', 'is_active', 'notes',
        'guacamole_connection_id', 'rdp_port',
    ];

    protected function casts(): array
    {
        return [
            'function'           => ServerFunction::class,
            'installed_services' => 'array',          // guardado como JSON, accedido como array
            'ssh_password'       => 'encrypted',      // Laravel encripta/desencripta automáticamente
            'is_active'          => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Server $server) {
            if (empty($server->slug)) {
                $server->slug = Str::slug($server->name);
            }
        });
    }

    // ── Relaciones ────────────────────────────────────────────────────

    public function ips()
    {
        return $this->hasMany(ServerIp::class);
    }

    public function privateIps()
    {
        return $this->hasMany(ServerIp::class)->where('type', 'private');
    }

    public function publicIps()
    {
        return $this->hasMany(ServerIp::class)->where('type', 'public');
    }

    public function primaryIp()
    {
        return $this->hasOne(ServerIp::class)->where('is_primary', true);
    }

    public function containers()
    {
        return $this->hasMany(ServerContainer::class);
    }

    public function activeContainers()
    {
        return $this->hasMany(ServerContainer::class)->where('is_active', true);
    }

    /**
     * Motores de base de datos corriendo en este servidor.
     */
    public function databaseServers()
    {
        return $this->hasMany(DatabaseServer::class);
    }

    /**
     * Despliegues de sistemas que corren en este servidor.
     */
    public function deployments()
    {
        return $this->hasMany(SystemInfrastructure::class);
    }

    public function responsibles()
    {
        return $this->hasMany(ServerResponsible::class)->with('persona')->orderBy('level');
    }

    public function activeResponsibles()
    {
        return $this->hasMany(ServerResponsible::class)->with('persona')->where('is_active', true)->orderBy('level');
    }

    /**
     * Sistemas alojados en este servidor (a través de system_infrastructure).
     */
    public function systems()
    {
        return $this->hasManyThrough(
            System::class,
            SystemInfrastructure::class,
            'server_id',   // FK en system_infrastructure → servers
            'id',          // PK en systems
            'id',          // PK en servers
            'system_id',   // FK en system_infrastructure → systems
        );
    }

    // ── Accessors ─────────────────────────────────────────────────────

    /**
     * Devuelve los servicios instalados como string legible.
     * Ej: ['Docker', 'Nginx'] → "Docker, Nginx"
     */
    public function getInstalledServicesStringAttribute(): string
    {
        return implode(', ', $this->installed_services ?? []);
    }

    /**
     * Detecta el protocolo Guacamole según el sistema operativo.
     * Windows → rdp  |  cualquier otro → ssh
     */
    public function getGuacamoleProtocolAttribute(): string
    {
        return str_contains(strtolower($this->operating_system ?? ''), 'windows')
            ? 'rdp'
            : 'ssh';
    }

    /**
     * Puerto por defecto según protocolo detectado.
     */
    public function getDefaultRemotePortAttribute(): int
    {
        return $this->guacamole_protocol === 'rdp' ? 3389 : 22;
    }
}
