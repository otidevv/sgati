<?php

namespace App\Models;

use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Model;

class SystemIntegration extends Model
{
    use LogsSystemActivity;
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

    protected function resolveActivitySystemId(): ?int
    {
        return $this->source_system_id ?? null;
    }

    protected function activitySubjectType(): string { return 'integración'; }
}
