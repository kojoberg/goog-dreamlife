<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
