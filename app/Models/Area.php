<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'acronym', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function systems()
    {
        return $this->hasMany(System::class);
    }
}
