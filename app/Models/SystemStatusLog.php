<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemStatusLog extends Model
{
    protected $fillable = [
        'system_id', 'old_status', 'new_status', 'changed_by', 'reason',
    ];

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
