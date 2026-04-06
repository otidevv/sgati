<?php

namespace App\Models;

use App\Enums\Environment;
use Illuminate\Database\Eloquent\Model;

class SystemVersion extends Model
{
    protected $fillable = [
        'system_id', 'version', 'release_date', 'environment',
        'changes', 'git_commit', 'git_branch', 'deployed_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'environment'  => Environment::class,
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function deployedBy()
    {
        return $this->belongsTo(User::class, 'deployed_by');
    }
}
