<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SystemService extends Model
{
    use LogsSystemActivity;
    protected $fillable = [
        'system_id', 'service_name', 'service_type', 'endpoint_url',
        'direction', 'auth_type', 'description', 'is_active',
        'environment', 'version',
        'provider_type', 'provider_system_id', 'provider_name',
        'valid_from', 'valid_until',
        'api_key', 'api_secret', 'token', 'token_expires_at',
        'requested_by_persona_id',
        // gateway
        'gateway_enabled', 'gateway_slug', 'gateway_require_key',
        'gateway_rate_per_minute', 'gateway_rate_per_day',
        'gateway_active_from', 'gateway_active_to',
    ];

    protected function casts(): array
    {
        return [
            'is_active'              => 'boolean',
            'valid_from'             => 'date',
            'valid_until'            => 'date',
            'token_expires_at'       => 'date',
            'api_key'                => 'encrypted',
            'api_secret'             => 'encrypted',
            'token'                  => 'encrypted',
            'gateway_enabled'        => 'boolean',
            'gateway_require_key'    => 'boolean',
        ];
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function providerSystem(): BelongsTo
    {
        return $this->belongsTo(System::class, 'provider_system_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'requested_by_persona_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SystemServiceDocument::class)->orderBy('created_at');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(SystemServiceField::class)->orderBy('sort_order')->orderBy('direction');
    }

    public function gatewayKeys(): HasMany
    {
        return $this->hasMany(ServiceGatewayKey::class, 'system_service_id')->orderByDesc('created_at');
    }

    public function gatewayLogs(): HasMany
    {
        return $this->hasMany(ServiceGatewayLog::class, 'system_service_id')->orderByDesc('created_at');
    }

    protected function ignoredForActivity(): array
    {
        return ['updated_at', 'created_at', 'deleted_at', 'api_key', 'api_secret', 'token', 'gateway_slug'];
    }

    protected function activitySubjectType(): string { return 'servicio'; }

    /** Genera y asigna un slug único para el gateway */
    public function generateGatewaySlug(): void
    {
        do {
            $slug = Str::slug($this->service_name) . '-' . Str::random(8);
        } while (self::where('gateway_slug', $slug)->exists());

        $this->gateway_slug = $slug;
    }
}
