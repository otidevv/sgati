<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemServiceField extends Model
{
    protected $fillable = [
        'system_service_id',
        'direction',
        'field_name',
        'field_type',
        'is_required',
        'description',
        'example_value',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(SystemService::class, 'system_service_id');
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'string'   => 'String',
            'integer'  => 'Integer',
            'boolean'  => 'Boolean',
            'number'   => 'Number',
            'array'    => 'Array',
            'object'   => 'Object',
            'date'     => 'Date',
            'datetime' => 'DateTime',
            'uuid'     => 'UUID',
            default    => 'Other',
        };
    }
}
