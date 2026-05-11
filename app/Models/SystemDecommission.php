<?php

namespace App\Models;

use App\Enums\SystemDecommissionReason;
use Illuminate\Database\Eloquent\Model;

class SystemDecommission extends Model
{
    protected $fillable = [
        'system_id',
        'reason_type',
        'reason',
        'effective_date',
        'shutdown_date',
        'resolution_number',
        'resolution_date',
        'resolution_entity',
        'memo_number',
        'memo_date',
        'authorized_by_name',
        'authorized_by_position',
        'registered_by_user_id',
        'successor_system_id',
        'successor_description',
        'data_migrated',
        'data_migration_destination',
        'data_retention_until',
        'users_affected',
        'audit_notes',
        'reactivation_procedure',
    ];

    protected function casts(): array
    {
        return [
            'reason_type'          => SystemDecommissionReason::class,
            'effective_date'       => 'date',
            'shutdown_date'        => 'date',
            'resolution_date'      => 'date',
            'memo_date'            => 'date',
            'data_retention_until' => 'date',
            'data_migrated'        => 'boolean',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function successorSystem()
    {
        return $this->belongsTo(System::class, 'successor_system_id');
    }
}
