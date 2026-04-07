<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerIp extends Model
{
    protected $table = 'server_ips';

    protected $fillable = [
        'server_id', 'ip_address', 'type', 'interface', 'is_primary', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePrivate($query)
    {
        return $query->where('type', 'private');
    }

    public function scopePublic($query)
    {
        return $query->where('type', 'public');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
