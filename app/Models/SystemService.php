<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemService extends Model
{
    protected $fillable = [
        'system_id', 'service_name', 'service_type', 'endpoint_url',
        'direction', 'auth_type', 'description', 'is_active',
        'environment', 'version',
        'provider_type', 'provider_system_id', 'provider_name',
        'valid_from', 'valid_until',
        'api_key', 'api_secret', 'token', 'token_expires_at',
        'requested_by_persona_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'valid_from'       => 'date',
            'valid_until'      => 'date',
            'token_expires_at' => 'date',
            'api_key'          => 'encrypted',
            'api_secret'       => 'encrypted',
            'token'            => 'encrypted',
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
}
