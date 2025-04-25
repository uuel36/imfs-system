<?php

namespace App\Models\Leadman;

use App\Models\HR\Area;
use App\Models\HR\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'team_id',
        'area_id',
        'team_members',
        'task_id',
        'data',
    ];

    public function team() {

        return $this->belongsTo(Team::class, 'team_id', 'id');

    }

    public function area() {

        return $this->belongsTo(Area::class, 'area_id', 'id');

    }

    public function task() {

        return $this->belongsTo(Task::class, 'task_id', 'id');

    }
}
