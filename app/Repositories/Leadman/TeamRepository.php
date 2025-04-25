<?php

namespace App\Repositories\Leadman;

use App\Models\Leadman\Team;
use App\Models\Leadman\TeamMember;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TeamRepository extends Repository {

    private $dailyOperationRepository;

    public function __construct(Team $model, DailyOperationRepository $dailyOperationRepository) {
        $this->dailyOperationRepository = $dailyOperationRepository;
        parent::__construct($model);

    }

    public function checkTeamName($team_name) {

        $team = DB::table('teams')->where('name', $team_name)->get();

    
        return $team;

    }

    public function getTeams($params) {
        $teams = $this->model()->with(['members' => function ($query) {
            $query->with(['employee']);
        }]);
    
        // List of team names and corresponding IDs that can bypass the filter
        $bypassTeams = [
            'Harvester 1' => 19,
            'Harvester 2' => 20,
            'Gas Prone' => 7,
            'Ground Spray' => 8,
            'Calib' => 9
        ];
    
        // Allow administrators (id:1) and warehouse staff (id:3) to bypass the filter
        if (Auth::id() !== 1 && Auth::id() !== 3) {
            // Check if the current team is in the bypass list, if not, apply the filter
            $teams->where(function($query) use ($bypassTeams) {
                $query->where('leadman_id', Auth::id())
                    ->orWhereIn('id', array_values($bypassTeams));
            });
        }
    
        if ($params->search) {
            $teams = $teams->where(function ($query) use ($params) {
                $query->where('name', 'LIKE', "%$params->search%");
            });
        }
    
        $teams = $teams->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
    
        return $teams;
    }
    

    public function getAllTeams() {

        $teams =  $this->model()->with(['members' => function ($query) {
            $query->with(['employee']);
        }])->orderBy('id', 'desc')->get();

        return $teams;

    }

    public function create_leadman($request){

    }

    public function storeTeams($request) {

        $data = new $this->model();
        $data->name = $request->name;
        $data->description = $request->description;
        $data->leadman_id = Auth::id();

        if($data->save()) {

            foreach($request->employees as $employee) {
                $member = new TeamMember();
                $member->team_id = $data->id;
                $member->employee_id = $employee->id;
                $member->save();
            }

            return $this->model()->with(['members' => function ($query) {
                $query->with(['employee']);
            }])->find($data->id);

        }

    }

    public function updateTeam($id, $request) {

        $data = $this->model()->find($id);
        $data->name = $request->name;
        $data->description = $request->description;
       
        
        // set is_deploy to 0 if team is updated where date is today

        DB::table('daily_operations')->where('team_id', $id)->whereDate('date', Carbon::today())->update(['is_deploy' => 0]);
    
    

        if($data->save()) {
            foreach($request->employees as $employee) {

                if($employee->status && $employee->status == 'new') {

                    $member = new TeamMember();
                    $member->team_id = $data->id;
                    $member->employee_id = $employee->id;
                    $member->save();
                }

            }

            foreach ($request->remove_members as $member) {

                $member = TeamMember::where('employee_id', $member)->where('team_id', $data->id)->first();
                if(!empty($member)) $member->delete();
            }

            $updatedTeam = $this->model()->with(['members' => function ($query) {
                $query->with(['employee']);
            }])->find($data->id);

            $this->dailyOperationRepository->updateTeamAndMeber($updatedTeam);

            return $updatedTeam;

        }

    }

    public function deleteTeam($id) {

        $data = $this->model()->find($id);

        TeamMember::where('team_id', $data->id)->delete();

        $data->delete();

    }

    public function searchTeam($params) {
        $teams = $this->model();
    
        // List of team names and corresponding IDs that can bypass the filter
        $bypassTeams = [
            'Harvester 1' => 19,
            'Harvester 2' => 20,
            'Gas Prone' => 7,
            'Ground Spray' => 8,
            'Calib' => 9
        ];
    
        // Allow administrators (id:1) and warehouse staff (id:3) to bypass the filter
        if (Auth::id() !== 1 && Auth::id() !== 3) {
            // Check if the current team is in the bypass list, if not, apply the filter
            $teams = $teams->where(function($query) use ($bypassTeams) {
                $query->where('leadman_id', Auth::id())
                    ->orWhereIn('id', array_values($bypassTeams));
            });
        }
    
        if($params->search) {
            $teams = $teams->where(function ($query) use ($params) {
                $query->where('name', 'LIKE', "%$params->search%");
            })
            ->with('members')
            ->limit(20)
            ->get();
        }
    
        return $teams;
    }
    
}
