<?php

namespace App\Models;

use App\Enums\DocType;
use Illuminate\Database\Eloquent\Model;

class SystemDocument extends Model
{
    protected $fillable = [
        'system_id', 'doc_type', 'title', 'doc_number', 'issuer',
        'issue_date', 'file_path', 'file_name', 'file_size',
        'mime_type', 'uploaded_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'doc_type'   => DocType::class,
            'issue_date' => 'date',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
