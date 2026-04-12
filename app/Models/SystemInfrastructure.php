<?php

namespace App\Models;

use App\Enums\Environment;
use Illuminate\Database\Eloquent\Model;

class SystemInfrastructure extends Model
{
    protected $table = 'system_infrastructure';

    protected $fillable = [
        'system_id', 'server_id',
        'public_ip', 'system_url', 'port', 'web_server',
        'ssl_enabled', 'ssl_expiry', 'ssl_certificate_id', 'ssl_custom_expiry',
        'environment', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'ssl_enabled'      => 'boolean',
            'ssl_expiry'       => 'date',
            'ssl_custom_expiry'=> 'date',
            'environment'      => Environment::class,
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function sslCertificate()
    {
        return $this->belongsTo(SslCertificate::class, 'ssl_certificate_id');
    }

    /**
     * Devuelve la fecha de vencimiento efectiva del SSL:
     * del certificado vinculado, del custom_expiry, o del legacy ssl_expiry.
     */
    public function effectiveSslExpiry(): ?\Illuminate\Support\Carbon
    {
        if ($this->ssl_certificate_id && $this->sslCertificate) {
            return $this->sslCertificate->valid_until;
        }
        return $this->ssl_custom_expiry ?? $this->ssl_expiry;
    }
}
