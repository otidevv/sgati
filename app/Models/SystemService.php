<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemService extends Model
{
    protected $fillable = [
        'system_id', 'service_name', 'service_type', 'endpoint_url',
        'direction', 'auth_type', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }
}
