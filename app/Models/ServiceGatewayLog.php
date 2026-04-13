<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGatewayLog extends Model
{
    public $timestamps = false;   // Solo tiene created_at manual

    protected $fillable = [
        'system_service_id',
        'gateway_key_id',
        'method',
        'path_info',
        'query_string',
        'ip_address',
        'response_status',
        'response_time_ms',
        'error_message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(SystemService::class, 'system_service_id');
    }

    public function gatewayKey(): BelongsTo
    {
        return $this->belongsTo(ServiceGatewayKey::class, 'gateway_key_id');
    }

    public function isSuccess(): bool
    {
        return $this->response_status >= 200 && $this->response_status < 400;
    }

    public function statusColor(): string
    {
        if (!$this->response_status) return 'gray';
        if ($this->response_status < 300) return 'emerald';
        if ($this->response_status < 400) return 'blue';
        if ($this->response_status < 500) return 'yellow';
        return 'red';
    }
}
