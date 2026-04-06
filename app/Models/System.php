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
            'status' => SystemStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($system) {
            if (empty($system->slug)) {
                $system->slug = Str::slug($system->name);
            }
        });
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

    public function statusLogs()
    {
        return $this->hasMany(SystemStatusLog::class)->orderByDesc('created_at');
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
