<?php

namespace App\Repositories\Leadman;

use App\Models\Leadman\Harvest;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HarvestRepository extends Repository {
    public function __construct(Harvest $model) {
        parent::__construct($model);
    }

    public function getHarvestByAreaNow($area) {

        $harvest = DB::table("harvests")->where('arae_id', $area)->whereDate('date', Carbon::now()->format('Y-m-d'))->pluck("stem_cut");
        //rror_log("harvest: " . $harvest);

        return  count($harvest) > 0 ? $harvest = $harvest[0] : $harvest = 0;

    }

    public function getHarvest($params) {


        // $harvest = DB::table("harvests")->select("harvests.*", "areas.name as area_name")
        // ->join("areas", "areas.id", "=", "harvests.arae_id");
        // join with harvests table

        $harvest = DB::table("harvests")->select("harvests.*", "areas.name as area_name")
        ->join("areas", "areas.id", "=", "harvests.arae_id");


        error_log("params: " . json_encode($harvest->get()));

        if($params->search) {

            $harvest = $harvest->where(function($query) use ($params) {
                $query->where(function($query) use ($params) {
                    $query->where('areas.name', 'LIKE', "%$params->search%");
                });
            })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $harvest;

        }

        $harvest = $harvest->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $harvest;

    }

    public function storeHarvest($request) {

        $data = new $this->model();

        $data->arae_id = $request->area_id;
        $data->stem_cut = $request->stem_cut;
        $data->date = $request->date;

        error_log("data: " . json_encode($data));

        if($data->save()) {
            return $this->model()->with(['area'])->find($data->id);
        }

    }

    public function updateHarvest($id, $request) {

        $data = $this->model()->find($id);
        $arae_id = $request->area_id_id ? $request->area_id_id : $request->area_id;

        $data->arae_id = $arae_id;
        $data->stem_cut = $request->stem_cut;
        $data->date = $request->date;

        if($data->save()) {
            return $this->model()->with(['area'])->find($id);
        }

    }

    public function deleteHarvest($id) {

        $data = $this->model()->find($id);

        $data->delete();

    }
}
