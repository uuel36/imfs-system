<?php

namespace App\Repositories\Leadman;

use App\Models\Leadman\DailyOperation;
use App\Models\Leadman\DailyOperationTeam;
use App\Models\Leadman\DailyOperationTeamMember;
use App\Models\Leadman\Team;
use App\Repositories\Repository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DailyOperationRepository extends Repository {

    public function __construct(DailyOperation $model) {

        parent::__construct($model);

    }

    public function getOperationDeployedTeamsByCurrentDate() {

        // get only team_id
        // $teams_id = DB::table('daily_operation')->where("date", Carbon::now()->format('Y-m-d'))

        // philippines time

        $time = Carbon::now()->timezone('Asia/Manila')->format('Y-m-d');



        $teams_id = DB::table('daily_operations')->where("date", $time)->where('is_deploy', 1)->pluck('team_id');
        error_log($time);
        $teams = DB::table('teams')->whereIn('id', $teams_id)->get();

        error_log($teams);

        return $teams;

    }

    public function getOperationByTeamDeployDate($id) {

        // get area name and task name

        $time = Carbon::now()->timezone('Asia/Manila')->format('Y-m-d');

        $operation = DB::table('daily_operations')->where("team_id", $id)->where("date", $time)->get();

        $area_name = DB::table('areas')->where("id", $operation[0]->area_id)->get();
        $task_name = DB::table('tasks')->where("id", $operation[0]->task_id)->get();

        $arr = array(
            'area' => [$operation[0]->area_id, $area_name[0]->name],
            'task' => [$operation[0]->task_id, $task_name[0]->name],
            'operation_id' => $operation,
        );

        return json_encode($arr);

    }



public function getOperations($params) {
    $operations = $this->model()->with(['dailyOperationTeam' => function($query) {
        $query->with(['dailyOperationTeamMember' => function($query) {
            $query->with(['employee']);
        }]);
    },'team', 'area', 'task'])
        ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'));

    // Allow administrators (id:1) and warehouse staff (id:3) to bypass the filter
    if (Auth::id() !== 1 && Auth::id() !== 3) {
        $operations->where('leadman_id', Auth::id());
    }

    if($params->search) {
        $operations = $operations->where(function($query) use ($params) {
            $query->whereHas('team', function($query) use ($params) {
                $query->where(function($query) use ($params) {
                    $query->where('name', 'LIKE', "%$params->search%");
                });
            });
            $query->whereHas('area', function($query) use ($params) {
                $query->where(function($query) use ($params) {
                    $query->where('name', 'LIKE', "%$params->search%");
                });
            });
            $query->whereHas('task', function($query) use ($params) {
                $query->where(function($query) use ($params) {
                    $query->where('name', 'LIKE', "%$params->search%");
                });
            });
        })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $operations;
    }

    $operations = $operations->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

    return $operations;
}


    public function getUndeployedOperations($params) {

        $operations = $this->model()->with(['dailyOperationTeam' => function($query) {
            $query->with(['dailyOperationTeamMember' => function($query) {
                $query->with(['employee']);
            }]);
        },'team', 'area', 'task'])
            ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'))
            ->where('is_deploy', 0);

        if($params->search) {
            $operations = $operations->where(function($query) use ($params) {
                $query->whereHas('team', function($query) use ($params) {
                    $query->where(function($query) use ($params) {
                        $query->where('name', 'LIKE', "%$params->search%");
                    });
                });
                $query->whereHas('area', function($query) use ($params) {
                    $query->where(function($query) use ($params) {
                        $query->where('name', 'LIKE', "%$params->search%");
                    });
                });
                $query->whereHas('task', function($query) use ($params) {
                    $query->where(function($query) use ($params) {
                        $query->where('name', 'LIKE', "%$params->search%");
                    });
                });
            })->get();

            return $operations;

        }

        $operations = $operations->get();

        return $operations;


    }

    public function storeOperation($request)
    {
        $check = DailyOperation::where('date', $request->date)
            ->where('team_id', $request->team_id)
            ->first();

        if (!empty($check)) {
            return 'already_time_in';
        }

        $data = new DailyOperation();
        $data->team_id = $request->team_id;
        $data->area_id = $request->area_id;
        $data->task_id = $request->task_id;
        $data->date = $request->date;
        $data->leadman_id = Auth::id();

        if ($data->save()) {
            $teamInfo = Team::with(['members'])->find($data->team_id);

            $team = new DailyOperationTeam();
            $team->daily_operation_id = $data->id;
            $team->team_id = $data->team_id;
            $team->name = $teamInfo->name;
            $team->description = $teamInfo->description;
    
            

            if ($team->save()) {
                foreach ($teamInfo->members as $member) {
                    $teamMember = new DailyOperationTeamMember();
                    $teamMember->d_o_team_id = $team->id;
                    $teamMember->employee_id = $member->employee_id;
                    $teamMember->save();
                }
            }

            return DailyOperation::with([
                'dailyOperationTeam' => function ($query) {
                    $query->with(['dailyOperationTeamMember' => function ($query) {
                        $query->with(['employee']); 
                    }]);
                },
                'area',
                'task'
            ])->find($data->id);
        }
    }

    public function updateOperation($id, $request) {

        $data = $this->model()->find($id);

        $team_id = $request->team_id_id ? $request->team_id_id : $request->team_id;
        $area_id = $request->area_id_id ? $request->area_id_id : $request->area_id;

        $data->team_id = $team_id;
        $data->area_id = $area_id;
        $data->date = $request->date;
        $data->members = $request->members;

        if($data->save()) {

            return $this->model()->with(['team', 'area', 'task'])->find($id);

        }

    }

    public function deleteOperation($id) {

        $data = $this->model()->find($id);
        $team = DailyOperationTeam::where('daily_operation_id', $id)->first();
        DailyOperationTeamMember::where('d_o_team_id', $team->id)->delete();
        DailyOperationTeam::where('daily_operation_id', $id)->delete();
        $data->delete();

    }

    public function generateReport($request) {

     

        $generate = $this->model()
                ->with(['team', 'area', 'task'])
                ->where('date', '>=', $request->date_from)
                ->where('date', '<=', $request->date_to)
                ->where('is_deploy', 1)
                ->where('area_id', $request->area_id)->get();
    

        foreach($generate as $gen) {

            $daily = DB::table("daily_reports")->where('date', $gen->date)->where('team_id', $gen->team_id)->first();

            if(isset($daily->data)){
                $gen->data = $daily->data;
            } else {
                $gen->data = 0;
            }

        }

        //error_log('generate report : ' . $generate);

        return $generate;

    }

    public function updateTeamAndMeber($params) {

        $operation = $this->model()
                ->where('team_id', $params->id)
                ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), Carbon::now()->format('d-m-Y'))->first();

        if(!empty($operation)) {

            $team_info = Team::with(['members'])->find($params->id);

            $team = DailyOperationTeam::where('daily_operation_id', $operation->id)->first();
            $team->name = $team_info->name;
            $team->description = $team_info->description;

            if($team->save()) {

                DailyOperationTeamMember::where('d_o_team_id', $team->id)->delete();
                foreach($team_info->members as $member) {
                    $team_member = new DailyOperationTeamMember();
                    $team_member->d_o_team_id = $team->id;
                    $team_member->employee_id = $member->employee_id;
                    $team_member->save();

                }

            }
        }
    }

}
