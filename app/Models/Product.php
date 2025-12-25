<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_chronic' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Helper to get total stock
    public function getStockAttribute()
    {
        return $this->batches()->where('expiry_date', '>=', now())->sum('quantity');
    }
}
