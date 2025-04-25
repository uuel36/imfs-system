<?php

namespace App\Repositories\Leadman;

use App\Models\HR\Employee;
use App\Models\Leadman\DailyReport;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyReportRepository extends Repository {

    public function __construct(DailyReport $model) {

        parent::__construct($model);

    }

    public function getReports($params) {

        date_default_timezone_set('Asia/Manila');

        $reports = DB::table('daily_reports')->where('date', (new Carbon($params->date))->format('Y-m-d'));

        //error_log($reports->get());

        // add team name, task name and area name using team_id, task_id and area_id respectively and json decode team_members
        // add area color using area_id
        $reports = $reports->join('teams', 'daily_reports.team_id', '=', 'teams.id')
        ->join('tasks', 'daily_reports.task_id', '=', 'tasks.id')
        ->join('areas', 'daily_reports.area_id', '=', 'areas.id')
        ->join('users', 'daily_reports.leadman_id', '=', 'users.id')
        ->select('daily_reports.*', 'teams.name as team_name', 'tasks.name as task_name', 'areas.color as area_color', 'areas.name as area_name', 'users.firstname as leadman_firstname', 'users.lastname as leadman_lastname', 'users.middlename as leadman_middlename', "users.id as leadman_id");



        error_log($reports->get());

        if($params->search) {

            $reports = $reports->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $reports;

        }

        $reports = $reports->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $reports;

    }

    public function getTeamReport($params) {

        date_default_timezone_set('Asia/Manila');

        // $reports = $this->model()
        //     ->with(['team', 'task'])
        //     ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'));

        // if($params->search) {

        //     $reports = $reports->whereHas('team', function ($query) use ($params) {
        //         $query->where(function ($query) use ($params) {
        //             $query->where('name', 'LIKE', "%$params->search%");
        //         });
        //     })
        //     ->selectRaw('sum(data) as sum, team_id')
        //     // ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'))
        //     ->groupBy('team_id')
        //     ->paginate($params->count, ['*'], 'page', $params->page);

        //     return $reports;
        // }

        // $reports = $reports
        //     ->selectRaw('sum(data) as sum, team_id, task_id, description')
        //     // ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'))
        //     ->groupBy(['team_id', 'task_id', 'description'])
        //     ->paginate($params->count, ['*'], 'page', $params->page);

        // return $reports;

        // rewrite everything above but using DB::table instead of eloquent
    
        if($params->search) {

            $reports = DB::table('daily_reports')
                ->join('teams', 'daily_reports.team_id', '=', 'teams.id')
                ->join('tasks', 'daily_reports.task_id', '=', 'tasks.id')
                ->selectRaw('sum(data) as sum, teams.name as team_name, tasks.name as task_name, tasks.description as task_description')
                ->where('teams.name', 'LIKE', "%$params->search%")
                ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'))
                ->groupBy(['teams.name', 'tasks.name', 'tasks.description'])
                ->paginate($params->count, ['*'], 'page', $params->page);

            return $reports;

        }

        $reports = DB::table('daily_reports')
            ->join('teams', 'daily_reports.team_id', '=', 'teams.id')
            ->join('tasks', 'daily_reports.task_id', '=', 'tasks.id')
            ->selectRaw('sum(data) as sum, teams.name as team_name, tasks.name as task_name, tasks.description as task_description')
            ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'))
            ->groupBy(['teams.name', 'tasks.name', 'tasks.description'])
            ->paginate($params->count, ['*'], 'page', $params->page);

        return $reports;

    }

    public function storeReport($request) {

        // $check = DB::table('daily_reports')->get();
            // ->where('employee_id', $request->employee_id)
            // ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($request->date))->format('d-m-Y'))
            // ->first();

        // error_log($check);
        // error_log("test1");

        $request = json_encode($request);

        $request = json_decode($request, true);

        // error_log(json_encode("team_id: " . $request["team_id"]));
        // error_log(json_encode("date: " . $request["date"]));
        // error_log(json_encode("employee_id: " . json_encode($request["employee_id"])));
        // error_log(json_encode("data: " . $request["data"]));
        // error_log(json_encode("task_id: " . $request["task_id"]));
        // error_log(json_encode("area_id: " . $request["area_id"]));
        


        date_default_timezone_set('Asia/Manila');
        // $check = $this->model()->where('employee_id', $request->employee_id)
        //     ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($request->date))->format('d-m-Y'))->first();

        $check = DB::table('daily_reports')->where('team_id', $request["team_id"])
            ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($request["date"]))->format('d-m-Y'))->first();
        error_log("checking");
        error_log(json_encode($check));

        if($check) {

            return 'duplicate';

        }

        // get task name using task id
        $task = DB::table('tasks')->where('id', $request["task_id"])->first();

        // check if task name is "harvesting"
        if($task->name == "Harvesting") {
            DB::table('harvests')->insert([
                'arae_id' => $request["area_id"],
                'date' => $request["date"],
                'stem_cut' => $request["data"]
            ]);
        }

        $report = new $this->model();

        $report->team_id = (int)$request["team_id"];

        $report->date = new Carbon($request["date"]);

        $report->team_members = json_encode($request["employee_id"]);

        $report->leadman_id = $request["leadman_id"];

        $report->data = $request["data"];

        $report->task_id = $request["task_id"];

        $report->area_id = $request["area_id"];

        $report->save();

        // error_log(json_encode($report));

        // try {
        //     $report->save();
        // } catch (\Exception $e) {
        //     // print out data
        //     error_log($e);
        // }


        // // save report using table

        // DB::table('daily_reports')->insert([
        //     'team_id' => $report->team_id,
        //     'date' => $report->date,
        //     'team_members' => $report->team_members,
        //     'data' => $report->data,
        //     'task_id' => $report->task_id,
        //     'area_id' => $report->area_id,
        //     'created_at' => $report->created_at
        // ]);

        error_log("saved");

        // return saved report

        $report = DB::table('daily_reports')->where('team_id', $request["team_id"])
            ->where(DB::raw("(DATE_FORMAT(date,'%d-%m-%Y'))"), (new Carbon($request["date"]))->format('d-m-Y'))->first();
        
        error_log(json_encode($report));
        return $report;

    }

    public function updateReport($id, $request) {
        date_default_timezone_set('Asia/Manila');

        error_log(json_encode($request));

        try {

            DB::table("daily_reports")->where('id', $id)->update([
                'team_id' => $request->team_id,
                'date' => new Carbon($request->date),
                'team_members' => json_encode($request->employee_id),
                'data' => $request->data,
                'task_id' => $request->task_id,
                'area_id' => $request->area_id,
                'updated_at' => Carbon::now()
            ]);

            $report = DB::table('daily_reports')->where('id', $id)->first();

            return $report;

        } catch (\Exception $e) {
            // print out data
            error_log($e);
        }

    }

    public function deleteReport($id) {

        $report = $this->model()->find($id);
        $report->delete();

    }

}
