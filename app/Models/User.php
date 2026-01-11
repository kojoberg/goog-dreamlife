<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission($permissionSlug)
    {
        // Admins and Super Admins have all permissions implicitly
        if ($this->isAdmin()) {
            return true;
        }

        // Pharmacists have specific permissions by default (core workflow only)
        if ($this->isPharmacist()) {
            $pharmacistPermissions = [
                'access_pos',
                'dispense_medication',
                'prescribe_medication',
                'register_patient',
                'receive_stock',
                // Note: 'manage_products' removed - now controlled via permission management
            ];
            if (in_array($permissionSlug, $pharmacistPermissions)) {
                return true;
            }
        }

        return $this->permissions->contains('slug', $permissionSlug);
    }

    public function givePermissionTo($permissionSlug)
    {
        $permission = Permission::where('slug', $permissionSlug)->firstOrFail();
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Check if user is a super admin (manages all branches).
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Check if user has admin privileges (admin or super_admin).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    public function isPharmacist()
    {
        return $this->role === 'pharmacist';
    }
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }
    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    public function isLabScientist()
    {
        return $this->role === 'lab_scientist';
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function hasOpenShift()
    {
        return $this->shifts()->whereNull('end_time')->exists();
    }

    /**
     * Get the branch ID to use for filtering queries.
     * Returns null for super admins (sees all).
     */
    public function getBranchIdForScope(): ?int
    {
        if ($this->isSuperAdmin()) {
            return null;
        }
        return $this->branch_id;
    }
}

