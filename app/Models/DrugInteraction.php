<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugInteraction extends Model
{
    protected $guarded = [];

    public function drugA()
    {
        return $this->belongsTo(Product::class, 'drug_a_id')->withTrashed();
    }

    public function drugB()
    {
        return $this->belongsTo(Product::class, 'drug_b_id')->withTrashed();
    }
}
