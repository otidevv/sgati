<?php

namespace App\Models;

use App\Enums\Environment;
use Illuminate\Database\Eloquent\Model;

class SystemDatabase extends Model
{
    protected $fillable = [
        'system_id', 'database_server_id',
        'db_name', 'engine', 'schema_name',
        'db_user', 'db_password',
        'responsible', 'environment', 'notes',
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
}
