<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemIntegration extends Model
{
    protected $fillable = [
        'source_system_id', 'target_system_id', 'connection_type',
        'description', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sourceSystem()
    {
        return $this->belongsTo(System::class, 'source_system_id');
    }

    public function targetSystem()
    {
        return $this->belongsTo(System::class, 'target_system_id');
    }
}
