<?php

namespace App\Models\HR;

use App\Models\Finance\Position;
use App\Models\Leadman\TeamMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $appends = array(
        'has_team_status'
    );

    public function teamMember() {

        return $this->belongsTo(TeamMember::class, 'id', 'employee_id');

    }

    public function position() {

        return $this->belongsTo(Position::class, 'position_id', 'id');

    }

    public function getHasTeamStatusAttribute() {

        return $this->hasTeam();
    }

    public function hasTeam() {

        $hasTeam = $this->teamMember()->find($this->id);


        if($hasTeam) {
            return 'hasTeam';
        }

        return 'noTeam';

    }
}
