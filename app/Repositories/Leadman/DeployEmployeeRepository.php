<?php

namespace App\Repositories\Leadman;

use App\Models\HR\Area;
use App\Models\Leadman\DailyOperation;
use App\Models\Leadman\DeployEmployee;
use App\Models\Leadman\Team;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Test;

class DeployEmployeeRepository extends Repository {

    public function __construct(DeployEmployee $model) {

        parent::__construct($model);

    }

    public function getLeadmans() {

        $leadmans = DB::table('users')->where('role_id', 2)->get();
        return $leadmans;
    }

    public function getDeployByTeamIdAndDate($params) {

        // $deploy = DB::table('daily_reports')->where('team_id', $id)->get();
        error_log("params: " . json_encode($params));

        $deploy = DB::table('daily_reports')->where('team_id', $params->teamId)->where('date', $params->date)->get();

        error_log("deploy: " . json_encode($deploy));

        return $deploy;
    }

    public function searchDeployByDate($id){
        

        $date = Carbon::now()->format('Y-m-d');



        $team_members_id = DB::table('team_members')->where('team_id', $id->team_id)->pluck("employee_id");
        $operation = DB::table('daily_operations')->where('team_id', $id->team_id)->where('date', $id->date)->first();

        // error_log("" . $team_members_id);
        error_log("operation: " . json_encode($operation));
        $team_members_info = DB::table('employees')->whereIn('id', $team_members_id)->get();
        // error_log("team_members_info: " . $team_members_info);
        $area_name = DB::table('areas')->where('id', $operation->area_id)->pluck("name");
        // error_log("" . $area_name);
        $task = DB::table('tasks')->where('id', $operation->task_id)->pluck("name");
        error_log("" . $task);

        //error_log("" . $team_members_info);
        $arr = ['team_members_info' => $team_members_info, 'team_members_id' => $team_members_id, "operation" => $operation, "info" => [['id' => $operation->area_id, 'name' => $area_name], ['id' => $operation->task_id, 'name' => $task]]];
        
        //error_log("task: " . json_encode($arr));
        return json_encode($arr);
    }

    public function getDeploy($params) {

        $employee =$this->model()->with(['area', 'team', 'dailyOperation' => function ($query) {
            $query->with(['dailyOperationTeam' => function ($query) {
                $query->with(['dailyOperationTeamMember' => function ($query) {
                    $query->with(['employee']);
                }]);
            }]);
        }])
        ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'));

        if($params->search) {

            $employee = $employee->where(function ($query) use ($params) {
                $query->whereHas('team', function ($query) use ($params) {
                    $query->where(function ($query) use ($params) {
                        $query->orWhere('name', 'LIKE', "%$params->search%");
                    });
                    $query->whereHas('area', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                });
            })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $employee;

        }

        $employee = $employee->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $employee;

    }

    public function getDeployByArea($params) {
        date_default_timezone_set('Asia/Manila');
        // $employee =$this->model()->with(['area', 'team', 'task' => function ($query) {
        //     $query->with(['task']);
        // }])
        // ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'))->get();

        // $employee = $employee->groupBy([function($val) {
        //     return $val->area->name;
        // }]);

        $teams = Area::with(['deployTeam' => function($query) use ($params){
            $query->with(['dailyOperation' => function($query) {
                $query->with(['dailyOperationTeam' => function ($query) {
                    $query->with(['dailyOperationTeamMember' => function ($query) {
                        $query->with(['employee']);
                    }]);
                }]);
            },'team', 'task' => function($query){
                $query->with(['task']);
            }])->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'));;
        }])->has('deployTeam')->get();


        return $teams;
    }

    public function storeDeploy($request) {
        $check = $this->model()
            ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($request->date))->format('d-m-Y'))
            ->where('team_id', $request->team_id)->first();

            if(!empty($check)) {

                return 'already_deploy';

            }

        $data = new $this->model();
        $data->team_id = $request->team_id;
        $data->area_id = $request->area_id;
        $data->daily_operation_id = $request->id;
        $data->date = $request->date;

        if($data->save()) {

            $daily = DailyOperation::find($request->id);
            $daily->is_deploy = true;
            $daily->save();

            return $this->model()->with(['area', 'team', 'task' => function ($query) {
                $query->with(['task']);
            }])->find($data->id);
        }

    }

    public function updateDeploy($id, $request) {

        $data = $this->model()->find($id);
        $team_id = $request->team_id_id ? $request->team_id_id : $request->team_id;
        $area_id = $request->area_id_id ? $request->area_id_id : $request->area_id;

        $data->team_id = $team_id;
        $data->area_id = $area_id;
        $data->members = $request->members;
        $data->date = $request->date;

        if($data->save()) {
            return $this->model()->with(['area', 'team'])->find($id);
        }
    }

    public function deleteDeploy($id) {

        $data = $this->model()->find($id);

        $daily = DailyOperation::find($data->daily_operation_id);
            $daily->is_deploy = false;
            $daily->save();

        if($data->delete()) {
            return ' delete';
        }

    }

}
