<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemActivityLog extends Model
{
    protected $fillable = [
        'system_id', 'causer_id', 'subject_type',
        'subject_id', 'event', 'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
