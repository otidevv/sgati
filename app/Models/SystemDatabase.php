<?php

namespace App\Models;

use App\Enums\Environment;
use Illuminate\Database\Eloquent\Model;

class SystemDatabase extends Model
{
    protected $fillable = [
        'system_id', 'db_name', 'engine', 'server_host', 'port',
        'schema_name', 'responsible', 'environment', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'environment' => Environment::class,
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }
}
