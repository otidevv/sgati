<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatabaseServerResponsibleDocument extends Model
{
    protected $fillable = [
        'database_server_responsible_id',
        'original_name',
        'file_path',
        'description',
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(DatabaseServerResponsible::class, 'database_server_responsible_id');
    }
}
