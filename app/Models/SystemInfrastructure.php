<?php

namespace App\Models;

use App\Enums\Environment;
use Illuminate\Database\Eloquent\Model;

class SystemInfrastructure extends Model
{
    protected $table = 'system_infrastructure';

    protected $fillable = [
        'system_id', 'server_name', 'server_os', 'server_ip', 'public_ip',
        'system_url', 'port', 'web_server', 'ssl_enabled', 'ssl_expiry',
        'environment', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'ssl_enabled' => 'boolean',
            'ssl_expiry'  => 'date',
            'environment' => Environment::class,
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }
}
