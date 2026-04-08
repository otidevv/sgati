<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerResponsibleDocument extends Model
{
    protected $fillable = [
        'server_responsible_id',
        'original_name',
        'file_path',
        'description',
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(ServerResponsible::class, 'server_responsible_id');
    }
}
