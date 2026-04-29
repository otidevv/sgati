<?php

namespace App\Models;

use App\Enums\RepoProvider;
use App\Traits\LogsSystemActivity;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    use LogsSystemActivity;
    protected $fillable = [
        'system_id', 'name', 'provider', 'provider_other', 'repo_url',
        'username', 'token', 'credential_type',
        'default_branch', 'repo_type', 'is_private',
        'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'provider'   => RepoProvider::class,
            'token'      => 'encrypted',   // encriptado automáticamente
            'is_private' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function collaborators()
    {
        return $this->hasMany(RepositoryCollaborator::class)->orderBy('assigned_at');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    /**
     * URL limpia sin credenciales incrustadas.
     * Ej: https://github.com/unamad-oti/sgati
     */
    protected function ignoredForActivity(): array
    {
        return ['updated_at', 'created_at', 'deleted_at', 'token'];
    }

    protected function activitySubjectType(): string { return 'repositorio'; }

    public function getCleanUrlAttribute(): ?string
    {
        if (!$this->repo_url) return null;

        $parsed = parse_url($this->repo_url);
        return ($parsed['scheme'] ?? 'https') . '://'
             . ($parsed['host'] ?? '')
             . ($parsed['path'] ?? '');
    }
}
