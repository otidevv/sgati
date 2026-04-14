<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGatewayKeyDocument extends Model
{
    protected $fillable = [
        'gateway_key_id',
        'original_name',
        'file_path',
        'description',
        'document_type',
        'document_number',
        'document_date',
        'document_notes',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    public function gatewayKey(): BelongsTo
    {
        return $this->belongsTo(ServiceGatewayKey::class, 'gateway_key_id');
    }
}
