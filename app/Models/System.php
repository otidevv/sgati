<?php

namespace App\Models;

use App\Enums\SystemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class System extends Model
{
    protected $fillable = [
        'name', 'slug', 'acronym', 'description', 'status',
        'area_id', 'responsible_id', 'tech_stack', 'repo_url', 'observations',
    ];

    protected function casts(): array
    {
        return [
            'status'     => SystemStatus::class,
            'tech_stack' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($system) {
            if (empty($system->slug)) {
                $system->slug = static::uniqueSlug(Str::slug($system->name));
            }
        });
    }

    public static function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function infrastructure()
    {
        return $this->hasOne(SystemInfrastructure::class);
    }

    /**
     * Servidor donde está alojado este sistema.
     * Atajo: $system->server en lugar de $system->infrastructure->server
     */
    public function server()
    {
        return $this->hasOneThrough(
            Server::class,
            SystemInfrastructure::class,
            'system_id',  // FK en system_infrastructure → systems
            'id',         // PK en servers
            'id',         // PK en systems
            'server_id',  // FK en system_infrastructure → servers
        );
    }

    public function versions()
    {
        return $this->hasMany(SystemVersion::class)->orderByDesc('release_date');
    }

    public function databases()
    {
        return $this->hasMany(SystemDatabase::class);
    }

    public function services()
    {
        return $this->hasMany(SystemService::class);
    }

    public function documents()
    {
        return $this->hasMany(SystemDocument::class)->orderByDesc('created_at');
    }

    public function responsibles()
    {
        return $this->hasMany(SystemResponsible::class)->with('persona', 'documents')->orderByDesc('assigned_at');
    }

    public function statusLogs()
    {
        return $this->hasMany(SystemStatusLog::class)->orderByDesc('created_at');
    }

    public function repositories()
    {
        return $this->hasMany(Repository::class);
    }

    public function containers()
    {
        return $this->hasMany(ServerContainer::class);
    }

    public function integrationsFrom()
    {
        return $this->hasMany(SystemIntegration::class, 'source_system_id');
    }

    public function integrationsTo()
    {
        return $this->hasMany(SystemIntegration::class, 'target_system_id');
    }
}
