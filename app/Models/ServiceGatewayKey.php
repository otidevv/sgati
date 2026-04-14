<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\System;
use App\Models\ServiceGatewayKeyDocument;

class ServiceGatewayKey extends Model
{
    protected $fillable = [
        'system_service_id',
        'name',
        'key_prefix',
        'key_hash',
        'gateway_slug',
        'is_active',
        'rate_per_minute',
        'rate_per_day',
        'expires_at',
        'allowed_ips',
        'persona_id',
        'last_used_at',
        'total_requests',
        'notes',
        // consumer info
        'consumer_type',
        'requesting_system_id',
        'consumer_organization',
        'purpose',
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

    public function requestingSystem(): BelongsTo
    {
        return $this->belongsTo(System::class, 'requesting_system_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ServiceGatewayLog::class, 'gateway_key_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ServiceGatewayKeyDocument::class, 'gateway_key_id')->orderBy('created_at', 'desc');
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

    /**
     * Genera un slug único para la URL del gateway de este consumidor.
     * Formato: {servicio-slug}-{sufijo-random-8}
     */
    public function generateGatewaySlug(): void
    {
        $base = Str::slug($this->name ?? 'consumidor');
        $base = substr($base, 0, 40); // máx 40 chars del nombre

        do {
            $slug = $base . '-' . Str::random(8);
        } while (self::where('gateway_slug', $slug)->exists());

        $this->gateway_slug = $slug;
    }

    /** URL pública completa del gateway para este consumidor */
    public function gatewayUrl(string $path = ''): string
    {
        $base = url('/api/gw/' . $this->gateway_slug);
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }
}
