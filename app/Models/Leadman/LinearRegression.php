<?php

namespace App\Models\Leadman;

use App\Models\HR\Area;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinearRegression extends Model
{
    use HasFactory;

    public function area() {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
}
