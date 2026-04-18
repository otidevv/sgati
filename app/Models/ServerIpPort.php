<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerIpPort extends Model
{
    protected $table = 'server_ip_ports';

    protected $fillable = [
        'server_ip_id', 'port', 'protocol', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function serverIp()
    {
        return $this->belongsTo(ServerIp::class);
    }
}
