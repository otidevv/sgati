<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SslCertificate extends Model
{
    protected $fillable = [
        'name', 'issuer', 'common_name',
        'valid_from', 'valid_until',
        'cert_file_path', 'key_file_path', 'chain_file_path', 'pfx_file_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'valid_from'  => 'date',
            'valid_until' => 'date',
        ];
    }

    public function infrastructures(): HasMany
    {
        return $this->hasMany(SystemInfrastructure::class, 'ssl_certificate_id');
    }

    /** Días hasta vencimiento (negativo si ya venció). */
    public function daysUntilExpiry(): ?int
    {
        return $this->valid_until ? now()->diffInDays($this->valid_until, false) : null;
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        $left = $this->daysUntilExpiry();
        return $left !== null && $left >= 0 && $left < $days;
    }
}
