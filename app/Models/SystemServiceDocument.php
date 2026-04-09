<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemServiceDocument extends Model
{
    protected $fillable = [
        'system_service_id',
        'document_type',
        'direction',
        'original_name',
        'file_path',
        'description',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(SystemService::class, 'system_service_id');
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'solicitud'    => 'Solicitud',
            'acta_entrega' => 'Acta de Entrega',
            'oficio'       => 'Oficio',
            'contrato'     => 'Contrato',
            'memo'         => 'Memorando',
            'resolucion'   => 'Resolución',
            'otro'         => 'Otro',
            default        => $type,
        };
    }
}
