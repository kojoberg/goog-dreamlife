<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];



    protected $casts = [
        'is_chronic' => 'boolean',
        'tax_exempt' => 'boolean',
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

    // Helper to get total stock (non-expired or no expiry date)
    public function getStockAttribute()
    {
        return $this->batches()
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->sum('quantity');
    }

    // Branch pivot relationship
    public function branches()
    {
        return $this->belongsToMany(Branch::class)
            ->withPivot(['unit_price', 'cost_price', 'reorder_level'])
            ->withTimestamps();
    }

    // Dynamic Price Accessor (Branch Aware)
    // NOTE: This assumes we store the current branch in session or config globally if we want magic access,
    // OR we explicitly call a method.
    // For safety, let's add a helper method rather than overriding the attribute which might confuse saving.
    public function getPriceForBranch($branchId)
    {
        $pivot = $this->branches()->where('branch_id', $branchId)->first();
        if ($pivot && $pivot->pivot->unit_price !== null) {
            return $pivot->pivot->unit_price;
        }
        return $this->unit_price;
    }

    public function getCostForBranch($branchId)
    {
        $pivot = $this->branches()->where('branch_id', $branchId)->first();
        if ($pivot && $pivot->pivot->cost_price !== null) {
            return $pivot->pivot->cost_price;
        }
        return $this->cost_price;
    }
}
