<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollArchive extends Model
{
    use HasFactory;

    protected $casts = [
        'regular' => 'array',
        'overtime' => 'array',
        'deductions' => 'array',
    ];

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
