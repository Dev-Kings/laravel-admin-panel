<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'department_id', 'rate_id', 'staff_no', 'employee_name'];

    public function presents()
    {
        return $this->belongsToMany(Present::class)->withPivot('days_present', 'total', 'date');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }

    public function scopeFilter($query, array $filters)
    {
        if ($filters['search'] ?? false) {
            $query->where('staff_no', 'like', '%' . request('search') . '%')
                ->orWhere('employee_name', 'like', '%' . request('search') . '%');
        }
    }
}
