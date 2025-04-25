<?php
namespace App\Repositories\Leadman;

use App\Models\Leadman\LinearRegression;
use App\Repositories\Repository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LinearRegressionRepository extends Repository {

    public function __construct(LinearRegression $model) {

        parent::__construct($model);

    }

    public function getForecast($id) {
        $fy = [];
    
        // Get data from getDataGraphByYear and getStemDataGraphByYear for the years 2023, 2024, and 2025
        $dataGraph = $this->getDataGraphByYear((object)['area_id' => $id->area_id, 'year' => $id->year - 3]);
        $stemDataGraph = $this->getStemDataGraphByYear((object)['area_id' => $id->area_id, 'year' => $id->year - 3]);
    
        for ($i = 1; $i <= 12; $i++) {
            $linears = $this->model()
                ->where('area_id', $id->area_id)
                ->whereYear('bu_injection_date', $id->year - 3) // Adjusted to pass the years 2023, 2024, and 2025
                ->whereMonth('bu_injection_date', $i)
                ->whereYear('bagging_report_date', $id->year - 3) // Adjusted to pass the years 2023, 2024, and 2025
                ->whereMonth('bagging_report_date', $i)
                ->get();

            if (!$linears->isEmpty()) {
                $countLinear = $linears->count();
                $sum_x1 = 0;
                $sum_x2 = 0;

                foreach ($linears as $key => $value) {
                   
                    $sum_x1 += $dataGraph[$i - 1]; 
                    $sum_x2 += $stemDataGraph[$i - 1]; 
                }

                $m1 = $sum_x1 / $countLinear;
                $m2 = $sum_x2 / $countLinear;

                $linears->map(function ($log) use ($m1, $m2) {
                    $log->x1m1 = $log->bud_injection_x1 - $m1;
                    $log->x2m2 = $log->bagging_report_x2 - $m2;
                    $log->x1m12 = $log->x1m1 * $log->x1m1;
                    $log->x2m22 = $log->x2m2 * $log->x2m2;
                    return $log;
                });

                $avr_x1m12 = 0;
                $avr_x2m22 = 0;

                foreach ($linears as $key => $value) {
                    $avr_x1m12 += $value->x1m12;
                    $avr_x2m22 += $value->x2m22;
                }

                $q1 = sqrt($avr_x1m12 / $countLinear);
                $q2 = sqrt($avr_x2m22 / $countLinear);

                $b1 = $q1 / $m1;
                $b2 = $q2 / $m2;

                $linears->map(function ($log) use ($b1, $b2) {
                    $log->y = round((($b1 * $log->bud_injection_x1) + ($b2 * $log->bagging_report_x2)), 0);
                });

                $logs = $linears->map(function ($log) {
                    $bud_inject_month = (new Carbon($log->bu_injection_date))->format('F');
                    $bud_inject_year = (new Carbon($log->bu_injection_date))->format('Y');
                    $bagging_report_month = (new Carbon($log->bagging_report_date))->format('F');
                    $bagging_report_year = (new Carbon($log->bagging_report_date))->format('Y');

                    $log->bud_inject_month = $bud_inject_month;
                    $log->bud_inject_year = $bud_inject_year;
                    $log->bagging_report_month = $bagging_report_month;
                    $log->bagging_report_year = $bagging_report_year;
                    $log->y = $log->y;

                    return $log;
                });

                $new_logs = [];

                $x1 = [];
                $x2 = [];
                $y = [];

                foreach ($logs as $key => $value) {
                    array_push($x1, [
                        'bud_inject_month' => $value['bud_inject_month'],
                        'bud_injection_x1' => $value['bud_injection_x1']
                    ]);
                }

                foreach ($logs as $key => $value) {
                    array_push($x2, [
                        'bagging_report_month' => $value['bagging_report_month'],
                        'bagging_report_x2' => $value['bagging_report_x2']
                    ]);
                }

                foreach ($logs as $key => $value) {
                    array_push($y, [
                        'y' => $value['y'],
                        'bagging_report_month' => $value['bagging_report_month'],
                    ]);
                }

                $x1 = collect($x1)->groupBy('bud_inject_month')
                    ->map(function ($item) {
                        return $item->sum('bud_injection_x1');
                    });

                $x2 = collect($x2)->groupBy('bagging_report_month')
                    ->map(function ($item) {
                        return $item->sum('bagging_report_x2');
                    });

                $y = collect($y)->groupBy('bagging_report_month')
                    ->map(function ($item) {
                        return $item->sum('y');
                    });

                $logs['year'] = $id->year;

                $y = $y->values();
                array_push($fy, $y[0]);

            } else {
                array_push($fy, 0);
            }
        }

        return $fy;
    }

    public function getDataGraphByYear($id) {
        $fy = [];
        for($i = 1; $i <= 12; $i++) {
            $linears = $this->model()
            ->where('area_id', $id->area_id)
            ->whereYear('bu_injection_date', $id->year) 
            ->whereMonth('bu_injection_date', $i)
            ->whereYear('bagging_report_date', $id->year) 
            ->whereMonth('bagging_report_date', $i)
            ->get();
        
        
            if (!$linears->isEmpty()) {
    
                $countLinear = $linears->count();
                $sum_x1 = 0;
                $sum_x2 = 0;
    
                foreach ($linears as $key => $value) {
                    $sum_x1 += $value->bud_injection_x1; 
                    $sum_x2 += $value->bagging_report_x2;
                }
    
                $m1 = $sum_x1 / $countLinear;
                $m2 = $sum_x2 / $countLinear;
    
                $linears->map(function ($log) use ($m1, $m2) {
                    $log->x1m1 = $log->bud_injection_x1 - $m1;
                    $log->x2m2 = $log->bagging_report_x2 - $m2;
                    $log->x1m12 = $log->x1m1 * $log->x1m1;
                    $log->x2m22 = $log->x2m2 * $log->x2m2;
    
                    return $log;
                });
    
                $avr_x1m12 = 0;
                $avr_x2m22 = 0;
    
                foreach ($linears as $key => $value) {
                    $avr_x1m12 += $value->x1m12;
                    $avr_x2m22 += $value->x2m22;
                }
    
                $q1 = sqrt($avr_x1m12 / $countLinear);
                $q2 = sqrt($avr_x2m22 / $countLinear);
    
                $b1 = $q1 / $m1;
                $b2 = $q2 / $m2;
    
                $linears->map(function ($log) use ($b1, $b2) {
                    $log->y = round((($b1 * $log->bud_injection_x1) + ($b2 * $log->bagging_report_x2)), 0);
                });
    
                $logs = $linears->map(function ($log) {
                    $bud_inject_month = (new Carbon($log->bu_injection_date))->format('F');
                    $bud_inject_year = (new Carbon($log->bu_injection_date))->format('Y');
                    $bagging_report_month = (new Carbon($log->bagging_report_date))->format('F');
                    $bagging_report_year = (new Carbon($log->bagging_report_date))->format('Y');
    
                    $log->bud_inject_month = $bud_inject_month;
                    $log->bud_inject_year = $bud_inject_year;
                    $log->bagging_report_month = $bagging_report_month;
                    $log->bagging_report_year = $bagging_report_year;
                    $log->y = $log->y;
    
                    return $log;
                });
    
                $new_logs = [];
    
                $x1 = [];
                $x2 = [];
                $y = [];
    
                foreach ($logs as $key => $value) {
                    array_push($x1, [
                        'bud_inject_month' => $value['bud_inject_month'],
                        'bud_injection_x1' => $value['bud_injection_x1']
                    ]);
                }
               
    
                foreach ($logs as $key => $value) {
                    array_push($x2, [
                        'bagging_report_month' => $value['bagging_report_month'],
                        'bagging_report_x2' => $value['bagging_report_x2']
                    ]);
                }
    
                foreach ($logs as $key => $value) {
                    array_push($y, [
                        'y' => $value['y'],
                        'bagging_report_month' => $value['bagging_report_month'],
                    ]);
                }
    
                $x1 = collect($x1)->groupBy('bud_inject_month')
                    ->map(function ($item) {
                        return $item->sum('bud_injection_x1');
                    });
    
                $x2 = collect($x2)->groupBy('bagging_report_month')
                    ->map(function ($item) {
                        return $item->sum('bagging_report_x2');
                    });
    
    
                $y = collect($y)->groupBy('bagging_report_month')
                    ->map(function ($item) {
                        return $item->sum('y');
                    });
    
                $logs['year'] = $id->year;
    
                // return only value of collection of $y
                $y = $y->values();
    
                array_push($fy, $y[0]);
    
            } else {
                array_push($fy, 0);
            }

            
        }
    
        return $fy;
    }
    
  
    public function getStemDataGraphByYear($id) {
        $stem_cut = [];
        error_log("test " . json_encode($id));
        

    
        for($i = 1; $i <= 12; $i++) {

        

            $st = DB::table("harvests")
                ->where('harvests.arae_id', $id->area_id)
                ->whereYear('harvests.date', ((int)$id->year)) 
                ->whereMonth('harvests.date', $i)
                ->sum('harvests.stem_cut');

            error_log($st);

            array_push($stem_cut, $st);

        }

        return $stem_cut;
    }

    public function getAllLinearRegressions() {
        return $this->model->all(); // Using all() to fetch all records. You might want to use get() if applying conditions.
    }

    public function getStemDataGraphByYearAll($id) {
        $stem_cut = [];
        error_log("test " . json_encode($id));
        

        // loop for 12 months and get data
        for($i = 1; $i <= 12; $i++) {


            $st = DB::table("harvests")
                ->whereYear('harvests.date', (int)$id->year)
                ->whereMonth('harvests.date', $i)
                ->sum('harvests.stem_cut');

            array_push($stem_cut, $st);

        }

        return $stem_cut;
    }


    public function getDataGraphByYearAll($id) {

       
        $fy = [];

            for($i = 1; $i <= 12; $i++) {
                $linears = $this->model()
            
                ->whereYear('bu_injection_date', $id->year - 3) 
                ->whereMonth('bu_injection_date', $i)
                ->whereYear('bagging_report_date', $id->year - 3) 
                ->whereMonth('bagging_report_date', $i)
                ->get();

            error_log(json_encode($linears) . " logistik");

            // error_log($id->year);
            // error_log(json_encode($logistics));
            // $logistics = $this->model()
            // ->where('area_id', $id->area_id)
            // ->whereYear('bu_injection_date', date('Y'))
            // ->whereMonth('bu_injection_date', date('m'))
            // ->whereYear('bagging_report_date', date('Y'))
            // ->whereMonth('bagging_report_date', date('m'))
            // ->get();

            if(!$linears->isEmpty()) {

                $countLinear = $linears->count();

                $sum_x1 = 0;
                $sum_x2 = 0;

                foreach ($linears as $key => $value) {
                    $sum_x1 += $value->bud_injection_x1;
                    $sum_x2 += $value->bagging_report_x2;
                }

                $m1 = $sum_x1 / $countLinear;
                $m2 = $sum_x2 / $countLinear;

                $linears->map(function($log) use ($m1, $m2) {

                    $log->x1m1 = $log->bud_injection_x1 - $m1;
                    $log->x2m2 = $log->bagging_report_x2 - $m2;
                    $log->x1m12 = $log->x1m1 * $log->x1m1;
                    $log->x2m22 = $log->x2m2 * $log->x2m2;

                    return $log;
                });

                $avr_x1m12 = 0;
                $avr_x2m22 = 0;

                foreach ($linears as $key => $value) {

                    $avr_x1m12 += $value->x1m12;
                    $avr_x2m22 += $value->x2m22;

                }

                $q1 = sqrt($avr_x1m12 / $countLinear);
                $q2 = sqrt($avr_x2m22 / $countLinear);

                $b1 = $q1 / $m1;
                $b2 = $q2 / $m2;

                $linears->map(function ($log) use ($b1, $b2) {
                    $log->y = round((($b1 * $log->bud_injection_x1) + ($b2 * $log->bagging_report_x2)), 0);
                });

                $logs =  $linears->map(function ($log) {

                        $bud_inject_month = (new Carbon($log->bu_injection_date))->format('F');
                        $bud_inject_year = (new Carbon($log->bu_injection_date))->format('Y');
                        $bagging_report_month = (new Carbon($log->bagging_report_date))->format('F');
                        $bagging_report_year = (new Carbon($log->bagging_report_date))->format('Y');

                        $log->bud_inject_month = $bud_inject_month;
                        $log->bud_inject_year = $bud_inject_year;
                        $log->bagging_report_month = $bagging_report_month;
                        $log->bagging_report_year = $bagging_report_year;
                        $log->y = $log->y;

                    return $log;

                });
                $new_logs = [];

                $x1 = [];
                $x2 = [];
                $y = [];

                foreach ($logs as $key => $value) {
                    array_push($x1, [
                        'bud_inject_month' => $value['bud_inject_month'],
                        'bud_injection_x1' => $value['bud_injection_x1']
                    ]);
                }

                foreach ($logs as $key => $value) {
                    array_push($x2, [
                        'bagging_report_month' => $value['bagging_report_month'],
                        'bagging_report_x2' => $value['bagging_report_x2']
                    ]);
                }


                foreach ($logs as $key => $value) {
                    array_push($y, [
                        'y' => $value['y'],
                        'bagging_report_month' => $value['bagging_report_month'],
                    ]);
                }

                $x1 = collect($x1)->groupBy('bud_inject_month')
                ->map(function ($item) {
                    return $item->sum('bud_injection_x1') ;
                });

                $x2 = collect($x2)->groupBy('bagging_report_month')
                ->map(function ($item) {
                    return $item->sum('bagging_report_x2');
                });


                $y = collect($y)->groupBy('bagging_report_month')
                ->map(function ($item) {
                    return $item->sum('y');
                });

                $logs['year'] = $id->year;

                // return only value of collection of $y
                $y = $y->values();

                array_push($fy, $y[0]);

            } else {
                array_push($fy, 0);
            }
        }
        return $fy;
    }

    public function calculateLinearValues() {
        $areaIds = $this->model()->pluck('area_id')->unique();
    
        $allLinearValues = [];
    
        foreach ($areaIds as $areaId) {
            $data = $this->model()->with(['area'])->where('area_id', $areaId)->get();
    
            if (!$data->isEmpty()) {
                $countLinear = $data->count();
                $sum_x1 = 0;
                $sum_x2 = 0;
    
                foreach ($data as $key => $value) {
                    $sum_x1 += $value->bud_injection_x1;
                    $sum_x2 += $value->bagging_report_x2;
                }
    
                $m1 = $sum_x1 / $countLinear;
                $m2 = $sum_x2 / $countLinear;
    
                $data->map(function ($log) use ($m1, $m2) {
                    $log->x1m1 = $log->bud_injection_x1 - $m1;
                    $log->x2m2 = $log->bagging_report_x2 - $m2;
                    $log->x1m12 = $log->x1m1 * $log->x1m1;
                    $log->x2m22 = $log->x2m2 * $log->x2m2;
    
                    return $log;
                });
    
                $avr_x1m12 = 0;
                $avr_x2m22 = 0;
    
                foreach ($data as $key => $value) {
                    $avr_x1m12 += $value->x1m12;
                    $avr_x2m22 += $value->x2m22;
                }
    
                $q1 = sqrt($avr_x1m12 / $countLinear);
                $q2 = sqrt($avr_x2m22 / $countLinear);
    
                $b1 = $q1 / $m1;
                $b2 = $q2 / $m2;
    
                $linearValues = [
                    'area_id' => $areaId,
                    'sum_X1' => $sum_x1,
                    'sum_X2' => $sum_x2,
                    'b1' => $b1,
                    'b2' => $b2,
                    'm1' => $m1,
                    'm2' => $m2,
                ];
    
                $allLinearValues[] = $linearValues;
    
                // Store the logistic values in the database
                $this->model()
                    ->where('area_id', $areaId)
                    ->update([
                        'sum_X1' => $sum_x1,
                        'sum_X2' => $sum_x2,
                        'b1' => $b1,
                        'b2' => $b2,
                        'm1' => $m1,
                        'm2' => $m2,
                    ]);
            }
        }
    
        return $allLinearValues;
    }
    
    
    public function getLinearRegresions($params) {

        $data = $this->model()->with(['area']);

        if(date('l') == 'Wednesday') {

            // get current date
            $to = date('Y-m-d');
            // deduct 6 days from $date
            $from = date('Y-m-d', strtotime($to. ' - 7 days'));

            $logdaily = DB::table('daily_reports')->whereBetween('date', [$from, $to])->orWhere('task_id', 5)->orWhere("task_id", 4)->get();

            // get all area id from datebase get only id
            $area_id = DB::table('areas')->get()->pluck('id');

            $tmp = [];

            // error_log(json_encode($logdaily));

            if(!$logdaily->isEmpty()){

                foreach($logdaily as $t){
                        
                    // error_log(json_encode($t));

                    try {

                        if($t->task_id == 5) {

                            if(isset($tmp[$t->area_id]["injection"])){
                                $tmp[$t->area_id]["injection"] += $t->data;
                            } else {
                                $tmp[$t->area_id]["injection"] = $t->data;
                            }
    
                        } else if($t->task_id == 4) {
    
                            if(isset($tmp[$t->area_id]["bagging"])){
                                $tmp[$t->area_id]["bagging"] += $t->data;
                            } else {
                                $tmp[$t->area_id]["bagging"] = $t->data;
                            }
    
                        }
                    } catch ( Exception $e){
                        error_log($e->getMessage());
                    }
                }
            }

            error_log(json_encode($tmp));

            try {
                foreach($tmp as $t => $k){
                    $area_id = $t;
                    $bud_injection_x1 = isset($tmp[$t]["injection"]) ? $tmp[$t]["injection"] : 0;
                    $bagging_report_x2 = isset($tmp[$t]["bagging"]) ? $tmp[$t]["bagging"] : 0;
                    $stem_cut_y = 0;
                    $bu_injection_date = date('Y-m-d');
                    $bagging_report_date = date('Y-m-d');
                    $stem_cut_y_date = date('Y-m-d');

                    // check if data exist and area
                    $check = DB::table('linear_regressions')->where('area_id', $area_id)->where('bu_injection_date', $bu_injection_date)->get();

                    error_log($check);

                    if($check->isEmpty()){
                        $data = new $this->model();
                        $data->area_id = $area_id;
                        $data->bud_injection_x1 = $bud_injection_x1;
                        $data->bu_injection_date = $bu_injection_date;
                        $data->bagging_report_x2 = $bagging_report_x2;
                        $data->bagging_report_date = $bagging_report_date;
                        $data->stem_cut_y = $stem_cut_y;
                        $data->stem_cut_y_date = $stem_cut_y_date;
                        $data->save();
                    }
                }
            } catch (Exception $e){
                error_log($e->getMessage());
            }

            
        }

        if ($params->area_id && $params->area_id != 'All') {
            $data = $data->where('area_id', $params->area_id);
        }
    
        if ($params->search) {
            $data = $data->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
        } else {
            $data = $data->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
        }
    
        if (!$data->isEmpty()) {
            $countLinear = $data->count();
            $sum_x1 = 0;
            $sum_x2 = 0;
    
            foreach ($data as $key => $value) {
                $sum_x1 += $value->bud_injection_x1;
                $sum_x2 += $value->bagging_report_x2;
            }
    
            $m1 = $sum_x1 / $countLinear;
            $m2 = $sum_x2 / $countLinear;
    
            $data->map(function ($log) use ($m1, $m2) {
                $log->x1m1 = $log->bud_injection_x1 - $m1;
                $log->x2m2 = $log->bagging_report_x2 - $m2;
                $log->x1m12 = $log->x1m1 * $log->x1m1;
                $log->x2m22 = $log->x2m2 * $log->x2m2;
    
                return $log;
            });
    
            $avr_x1m12 = 0;
            $avr_x2m22 = 0;
    
            foreach ($data as $key => $value) {
                $avr_x1m12 += $value->x1m12;
                $avr_x2m22 += $value->x2m22;
            }
    
            $q1 = sqrt($avr_x1m12 / $countLinear);
            $q2 = sqrt($avr_x2m22 / $countLinear);
    
            $b1 = $q1 / $m1;
            $b2 = $q2 / $m2;
    
            $data->map(function ($log) use ($b1, $b2) {
                $log->y = round((($b1 * $log->bud_injection_x1) + ($b2 * $log->bagging_report_x2)), 0);
                return $log;
            });
        }
    
        return $data;
    }





   
    public function storeLinear($request) {
        $data = new $this->model();
    
        $data->bud_injection_x1 = $request->bud_injection_x1;
        $data->area_id = $request->area_id;
        $data->bu_injection_date = $request->bu_injection_date;
        $data->bagging_report_x2 = $request->bagging_report_x2;
    
        $data->bagging_report_date = $request->bagging_report_date;
    
        $data->stem_cut_y = $request->stem_cut_y;

        $linearValues = $this->calculateLinearValues();
        $currentLinearValues = collect($linearValues)->where('area_id', $request->area_id)->first();
    
        if ($currentLinearValues) {
            $data->sum_X1 = $currentLinearValues['sum_X1'];
            $data->sum_X2 = $currentLinearValues['sum_X2'];
            $data->b1 = $currentLinearValues['b1'];
            $data->b2 = $currentLinearValues['b2'];
            $data->m1 = $currentLinearValues['m1'];
            $data->m2 = $currentLinearValues['m2'];
            
            $data->x1m1 = $request->bud_injection_x1 - $currentLinearValues['m1'];
            $data->x2m2 = $request->bagging_report_x2 - $currentLinearValues['m2'];
            $data->x1m12 = $data->x1m1 * $data->x1m1;
            $data->x2m22 = $data->x2m2 * $data->x2m2;
        }
    
        if ($data->save()) {
            return $this->model()->with(['area'])->find($data->id);
        }
    }

    public function updateLinear($id, $request) {

        $area_id = $request->area_id_id ? $request->area_id_id : $request->area_id;

        $data = $this->model()->find($id);

        $data->bud_injection_x1 = $request->bud_injection_x1;
        $data->bu_injection_date = $request->bu_injection_date;
        $data->bagging_report_x2 = $request->bagging_report_x2;
        $data->area_id = $area_id;
        $data->bagging_report_date = $request->bagging_report_date;
        $data->stem_cut_y = $request->stem_cut_y;
        $data->stem_cut_y_date = $request->stem_cut_y_date;

        if($data->save()) {

            return  $this->model()->with(['area'])->find($id);

        }
    }

    public function deleteLinear($id)
    {

        $this->model()->find($id)->delete();

    }

    public function getData($id) {


        $l = $this->model()->where('area_id', $id)->get();

        if(!$l->isEmpty()) {

            $count = $l->count();

            $sum_x1 = 0;
            $sum_x2 = 0;

            foreach ($l as $key => $value) {
                $sum_x1 += $value->bud_injection_x1;
                $sum_x2 += $value->bagging_report_x2;
            }

            $m1 = $sum_x1 / $count;
            $m2 = $sum_x2 / $count;

            $l->map(function($log) use ($m1, $m2) {

                $log->x1m1 = $log->bud_injection_x1 - $m1;
                $log->x2m2 = $log->bagging_report_x2 - $m2;
                $log->x1m12 = $log->x1m1 * $log->x1m1;
                $log->x2m22 = $log->x2m2 * $log->x2m2;

                return $log;
            });

            $avr_x1m12 = 0;
            $avr_x2m22 = 0;

            foreach ($l as $key => $value) {

                $avr_x1m12 += $value->x1m12;
                $avr_x2m22 += $value->x2m22;

            }

            $q1 = sqrt($avr_x1m12 / $count);
            $q2 = sqrt($avr_x2m22 / $count);

            $b1 = $q1 / $m1;
            $b2 = $q2 / $m2;

            $l->map(function ($log) use ($b1, $b2) {
                $log->y = round((($b1 * $log->bud_injection_x1) + ($b2 * $log->bagging_report_x2)), 0);
            });

            return $l;
        }

        return [];
       

    }


    public function getDataGraph($id) {

        $xxx1 = 0;
        $xxx2 = 0;
        $yyy = 0;

        // loop for 12 months and get data
        for($i = 1; $i <= 12; $i++) {

            // turn $i into date('m')


            $linears = $this->model()->where('area_id', $id->area_id)->whereYear('bu_injection_date', $id->year)->whereMonth('bu_injection_date', $i)->whereYear('bagging_report_date', $id->year)->whereMonth('bagging_report_date', $i)->get();

            error_log(json_encode($linears) . " logistik");

            if(!$linears->isEmpty()) {

                $countLinear = $linears->count();

                $sum_x1 = 0;
                $sum_x2 = 0;

                foreach ($linears as $key => $value) {
                    $sum_x1 += $value->bud_injection_x1;
                    $sum_x2 += $value->bagging_report_x2;
                }

                $m1 = $sum_x1 / $countLinear;
                $m2 = $sum_x2 / $countLinear;

                $linears->map(function($log) use ($m1, $m2) {

                    $log->x1m1 = $log->bud_injection_x1 - $m1;
                    $log->x2m2 = $log->bagging_report_x2 - $m2;
                    $log->x1m12 = $log->x1m1 * $log->x1m1;
                    $log->x2m22 = $log->x2m2 * $log->x2m2;

                    return $log;
                });

                $avr_x1m12 = 0;
                $avr_x2m22 = 0;

                foreach ($linears as $key => $value) {

                    $avr_x1m12 += $value->x1m12;
                    $avr_x2m22 += $value->x2m22;

                }

                $q1 = sqrt($avr_x1m12 / $countLinear);
                $q2 = sqrt($avr_x2m22 / $countLinear);

                $b1 = $q1 / $m1;
                $b2 = $q2 / $m2;

                $linears->map(function ($log) use ($b1, $b2) {
                    $log->y = round((($b1 * $log->bud_injection_x1) + ($b2 * $log->bagging_report_x2)), 0);
                });

                $logs =  $linears->map(function ($log) {

                        $bud_inject_month = (new Carbon($log->bu_injection_date))->format('F');
                        $bud_inject_year = (new Carbon($log->bu_injection_date))->format('Y');
                        $bagging_report_month = (new Carbon($log->bagging_report_date))->format('F');
                        $bagging_report_year = (new Carbon($log->bagging_report_date))->format('Y');

                        $log->bud_inject_month = $bud_inject_month;
                        $log->bud_inject_year = $bud_inject_year;
                        $log->bagging_report_month = $bagging_report_month;
                        $log->bagging_report_year = $bagging_report_year;
                        $log->y = $log->y;

                    return $log;

                });
                $new_logs = [];

                $x1 = [];
                $x2 = [];
                $y = [];

                foreach ($logs as $key => $value) {
                    array_push($x1, [
                        'bud_inject_month' => $value['bud_inject_month'],
                        'bud_injection_x1' => $value['bud_injection_x1']
                    ]);
                }

                foreach ($logs as $key => $value) {
                    array_push($x2, [
                        'bagging_report_month' => $value['bagging_report_month'],
                        'bagging_report_x2' => $value['bagging_report_x2']
                    ]);
                }


                foreach ($logs as $key => $value) {
                    array_push($y, [
                        'y' => $value['y'],
                        'bagging_report_month' => $value['bagging_report_month'],
                    ]);
                }

                $x1 = collect($x1)->groupBy('bud_inject_month')
                ->map(function ($item) {
                    return $item->sum('bud_injection_x1') ;
                });

                $x2 = collect($x2)->groupBy('bagging_report_month')
                ->map(function ($item) {
                    return $item->sum('bagging_report_x2');
                });


                $y = collect($y)->groupBy('bagging_report_month')
                ->map(function ($item) {
                    return $item->sum('y');
                });

                $logs['year'] = (new Carbon())->format('Y');

                // return only value of collection of $y
                $yy = $y->values();
                $xx1 = $x1->values();
                $xx2 = $x2->values();

                

                error_log($yy[0] . " y");
                error_log($xx1[0] . " x1");
                error_log($xx2[0] . " x2");

                $yyy += (int) $yy[0];
                $xxx1 += (int) $xx1[0];
                $xxx2 += (int) $xx2[0];

            } 
        }

        return ['x1' => $xxx1, 'x2' => $xxx2, 'y' => $yyy, "year" => $id->year];

       
    }
}
