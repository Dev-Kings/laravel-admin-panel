<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Present extends Model
{
    use HasFactory;

    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withPivot('days_present', 'total', 'date');
    }
}
