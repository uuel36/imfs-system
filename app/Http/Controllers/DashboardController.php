<?php

namespace App\Http\Controllers;

use App\Models\HR\Area;
use App\Models\HR\Employee;
use App\Models\Leadman\Attendance;
use App\Models\Leadman\DailyOperation;
use App\Models\Leadman\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getTotalEmployees() {

        $employees = Employee::count();

        return response()->json($employees, 200);

    }

    public function getWarehouseData(){

        $warehouse = DB::table("stocks")->get();
        $returned = DB::table("deploys")->where("is_returned", 1)->get();
        $total_chemicals = DB::table('items')->join('categories', 'items.category_id', '=', 'categories.id')->where('categories.name', 'Chemicals')->count();
        $total_tools = DB::table('items')->join('categories', 'items.category_id', '=', 'categories.id')->where('categories.name', 'Tools')->count();
        // date is today

        // $chemicals_deployed = DB::table('deploys')->join('items', 'deploys.item_id', '=', 'items.id')->join('categories', 'items.category_id', '=', 'categories.id')->where('categories.name', 'Chemicals')->count();
        // $tools_deployed = DB::table('deploys')->join('items', 'deploys.item_id', '=', 'items.id')->join('categories', 'items.category_id', '=', 'categories.id')->where('categories.name', 'Tools')->count();

        // date is today
        $chemicals_deployed = DB::table('deploys')->join('items', 'deploys.item_id', '=', 'items.id')->join('categories', 'items.category_id', '=', 'categories.id')->where('categories.name', 'Chemicals')->where('deploys.created_at', '>=', Carbon::today())->count();
        $tools_deployed = DB::table('deploys')->join('items', 'deploys.item_id', '=', 'items.id')->join('categories', 'items.category_id', '=', 'categories.id')->where('categories.name', 'Tools')->where('deploys.created_at', '>=', Carbon::today())->count();
        
        $data = ['warehouse' => $warehouse, 'returned' => $returned, 'total_chemicals' => $total_chemicals, 'total_tools' => $total_tools, 'chemicals_deployed' => $chemicals_deployed, 'tools_deployed' => $tools_deployed];
        return response()->json($data, 200);
    }

    public function getOvertimeEmployees() {

        date_default_timezone_set('Asia/Manila');

        $date = Carbon::now()->format('Y-m-d');

        $employees = Attendance::where('date', $date)->where('ot_status', "Approved")->count();

        return response()->json($employees, 200);

    }

    public function getTotalAreas() {

        $areas = Area::count();

        return response()->json($areas, 200);

    }

    public function getTotalOperations() {

        date_default_timezone_set('Asia/Manila');

        $date = Carbon::now()->format('Y-m-d');

        $teams = DailyOperation::where('date', $date)->where('is_deploy', 1)->count();

        return response()->json($teams, 200);

    }

    public function getPresentEmployee() {

        date_default_timezone_set('Asia/Manila');


        $date = Carbon::now()->format('Y-m-d');

        $employees = Attendance::where('date', $date)->count();

        return response()->json($employees, 200);

    }

    public function getTotalTeams() {

        $teams = Team::count();

        return response()->json($teams, 200);

    }
}
