<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // use \App\Traits\HasBranchScope; // Removed: sales table lacks branch_id

    protected $fillable = [
        'user_id',
        'patient_id',
        'prescription_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_tendered',
        'change_amount',
        'payment_method',
        'points_redeemed',
        'points_earned',
        'tax_breakdown',
        'shift_id',
        'status'
    ];

    protected $casts = [
        'tax_breakdown' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Alias for patient, required for Shift Reports (admin.shifts.show)
     */
    public function customer()
    {
        return $this->patient();
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
