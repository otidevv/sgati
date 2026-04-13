<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceGatewayKey extends Model
{
    protected $fillable = [
        'system_service_id',
        'name',
        'key_prefix',
        'key_hash',
        'is_active',
        'rate_per_minute',
        'rate_per_day',
        'expires_at',
        'allowed_ips',
        'persona_id',
        'last_used_at',
        'total_requests',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active'     => 'boolean',
            'expires_at'    => 'datetime',
            'last_used_at'  => 'datetime',
            'allowed_ips'   => 'array',
            'total_requests' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(SystemService::class, 'system_service_id');
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ServiceGatewayLog::class, 'gateway_key_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /** Verifica si la raw key entrante corresponde a este registro */
    public function matchesRawKey(string $rawKey): bool
    {
        return hash('sha256', $rawKey) === $this->key_hash;
    }
}
