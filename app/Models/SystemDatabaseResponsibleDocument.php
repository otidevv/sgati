<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemDatabaseResponsibleDocument extends Model
{
    protected $fillable = [
        'system_database_responsible_id',
        'original_name',
        'file_path',
        'description',
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(SystemDatabaseResponsible::class, 'system_database_responsible_id');
    }
}
