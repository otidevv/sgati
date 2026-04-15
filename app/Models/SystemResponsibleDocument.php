<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemResponsibleDocument extends Model
{
    protected $fillable = [
        'system_responsible_id',
        'original_name',
        'file_path',
        'description',
        'document_type',
        'document_number',
        'document_date',
        'document_notes',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(SystemResponsible::class, 'system_responsible_id');
    }
}
