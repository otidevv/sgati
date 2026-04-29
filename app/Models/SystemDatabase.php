<?php

namespace App\Models;

use App\Enums\Environment;
use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemDatabase extends Model
{
    use LogsSystemActivity;
    protected $fillable = [
        'system_id', 'database_server_id',
        'db_name', 'engine', 'schema_name',
        'db_user', 'db_password',
        'environment', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'environment' => Environment::class,
            'db_password' => 'encrypted',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function databaseServer()
    {
        return $this->belongsTo(DatabaseServer::class);
    }

    public function responsibles(): HasMany
    {
        return $this->hasMany(SystemDatabaseResponsible::class)->orderBy('assigned_at');
    }

    protected function ignoredForActivity(): array
    {
        return ['updated_at', 'created_at', 'deleted_at', 'db_password'];
    }

    protected function activitySubjectType(): string { return 'base de datos'; }
}
