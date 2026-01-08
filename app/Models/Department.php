<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function headOfDepartment()
    {
        return $this->belongsTo(User::class, 'head_of_department_id');
    }

    public function employees()
    {
        return $this->hasMany(EmployeeProfile::class);
    }
}
