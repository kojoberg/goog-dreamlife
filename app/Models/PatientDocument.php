<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientDocument extends Model
{
    protected $guarded = [];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
