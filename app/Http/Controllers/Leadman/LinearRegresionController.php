<?php

namespace App\Http\Controllers\Leadman;

use App\Http\Controllers\Controller;
use App\Repositories\Leadman\LinearRegressionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LinearRegresionController extends Controller
{

    private $linearRegressionRepository;

    public function __construct(LinearRegressionRepository $linearRegressionRepository) {

        $this->linearRegressionRepository = $linearRegressionRepository;

    }

    public function index() {

        return view('leadman.linear.index');

    }

    public function calculateLinearValues(Request $request)
    {
        $linearValues = $this->linearRegressionRepository->calculateLinearValues();

        // You can return the $linearValues or use them as needed in your application
        return response()->json($linearValues, 200);
    }

    public function generateForecastData($id) {
        try {
            // Call the method from the repository to generate forecast data
            $forecastData = $this->linearRegressionRepository->generateForecastData($id);
    
            // Return the forecast data as JSON response
            return response()->json($forecastData, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            error_log($e->getMessage());
            return response()->json(['error' => 'An error occurred while generating forecast data.'], 500);
        }
    }
    public function predictAndStore(Request $request)
    {
        // Validate request data as needed

        // Call the forecastData method to predict and store the result
        $predictedValue = $this->linearRegressionRepository->forecastData($request->all());

        // Optionally, return a response or perform additional actions
        return response()->json(['predicted_value' => $predictedValue]);
    }

    public function forecastLinearValues($areaId)
{
    try {
        // Call the method from the repository to forecast linear values
        $forecastedValues = $this->linearRegressionRepository->forecastLinearValues($areaId);

        // Return the forecasted values as JSON response
        return response()->json($forecastedValues, 200);
    } catch (\Exception $e) {
        // Handle any exceptions that may occur
        error_log($e->getMessage());
        return response()->json(['error' => 'An error occurred while forecasting linear values.'], 500);
    }
}

    
    public function getLinears(Request $request) {

        $page = $request->page ? $request->page : 1;
        $area_id = $request->area_id ? $request->area_id : 'All';
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
            'area_id' => $area_id
        ];
        
        $linears = $this->linearRegressionRepository->getLinearRegresions(json_decode(json_encode($request->all())));

        // error_log(json_encode($linears));

        $linears->map(function($d) {

            $d->x1y = $d->bud_injection_x1 * $d->stem_cut_y;
            $d->x2y = $d->bagging_report_x2 * $d->stem_cut_y;
            $d->x1x2 = $d->bud_injection_x1 * $d->bagging_report_x2;
            $d->x12 = $d->bud_injection_x1 * $d->bud_injection_x1;
            $d->x22 = $d->bagging_report_x2 * $d->bagging_report_x2;
            $d->y22 = $d->stem_cut_y * $d->stem_cut_y;

            return $d;
        });

        return response()->json($linears, 200);

    }

    public function storeLinear(Request $request) {

        $data = $this->linearRegressionRepository->storeLinear(json_decode(json_encode($request->all())));

        $data->x1y = $data->bud_injection_x1 * $data->stem_cut_y;
        $data->x2y = $data->bagging_report_x2 * $data->stem_cut_y;
        $data->x1x2 = $data->bud_injection_x1 * $data->bagging_report_x2;
        $data->x12 = $data->bud_injection_x1 * $data->bud_injection_x1;
        $data->x22 = $data->bagging_report_x2 * $data->bagging_report_x2;
        $data->y22 = $data->stem_cut_y * $data->stem_cut_y;

        return response()->json($data, 200);

    }

    public function updateLinear($id, Request $request) {

        $data = $this->linearRegressionRepository->updateLinear($id, json_decode(json_encode($request->all())));

        $data->x1y = $data->bud_injection_x1 * $data->stem_cut_y;
        $data->x2y = $data->bagging_report_x2 * $data->stem_cut_y;
        $data->x1x2 = $data->bud_injection_x1 * $data->bagging_report_x2;
        $data->x12 = $data->bud_injection_x1 * $data->bud_injection_x1;
        $data->x22 = $data->bagging_report_x2 * $data->bagging_report_x2;
        $data->y22 = $data->stem_cut_y * $data->stem_cut_y;

        return response()->json($data, 200);

    }

    public function deleteLinear($id) {

        $data = $this->linearRegressionRepository->deleteLinear($id);

        return response()->json($data, 200);

    }

    public function getData($id) {

        $data = $this->linearRegressionRepository->getData($id);

        return response()->json($data, 200);

    }

    public function getDataGraph(Request $request) {
        try {
            $req = $request->all();

            // error_log(json_encode($req));

            //convert example Wed Mar 01 2023 00:00:00 GMT 0800 (Philippine Standard Time) into year and month
            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');

            $params = [
                'month' => $month,
                'year' => $year,
                'area_id' => $request['area_id']
            ];

            // $this->getDataGraphForTwoYears();

            // error_log(json_encode($params));

            $data = $this->linearRegressionRepository->getDataGraph(json_decode(json_encode($params)));

            // error_log(json_encode($data));

            return response()->json($data, 200);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function trainAndPredict($id) {
        try {
            // Call the method from the repository to train the model and make predictions
            $predictions = $this->linearRegressionRepository->trainAndPredict($id);
        
            // Return the predictions as JSON response
            return response()->json($predictions, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            error_log($e->getMessage());
            return response()->json(['error' => 'An error occurred while training and making predictions.'], 500);
        }
    }
  

    public function getDataGraphByYear(Request $request){
        try {
            $req = $request->all();
    
            $area_id = $req['area_id'] ? $req['area_id'] : 'All';
            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');
    
            $params = [
                'area_id' => $area_id,
                'year' => $year
            ];
    
            $dt = $this->linearRegressionRepository->getDataGraphByYear(json_decode(json_encode($params)));
    
            return response()->json( $dt, 200);
    
        } catch (\Exception $e) {
            // Handle exceptions
        }
    }

    
    public function getForecast(Request $request){
        try {
            $req = $request->all();
    
            $area_id = $req['area_id'] ? $req['area_id'] : 'All';
            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');
    
            $params = [
                'area_id' => $area_id,
                'year' => $year
            ];
    
            $dt = $this->linearRegressionRepository->getForecast(json_decode(json_encode($params)));
    
            return response()->json( $dt, 200);
    
        } catch (\Exception $e) {
            // Handle exceptions
        }
    }

  
 
    
    
    
    
    
    public function getAllLinearRegressions() {
        // Fetch all linear regressions using the repository
        $linearRegressions = $this->linearRegressionRepository->getAllLinearRegressions();
        
        // Return the linear regressions as a JSON response
        return response()->json($linearRegressions);
    }
    

    public function predictAndShow(Request $request)
    {
        try {
            // Validate request data as needed
    
            // Extract input data
            $areaId = $request->area_id;
            $data = $request->data;
            $yValues = $request->y_values;
    
            // Call the predictYValue method to make predictions
            $predictions = $this->linearRegressionRepository->predictYValue($areaId, $data, $yValues);
        
            // Prepare the response data
            $result = [
                'predictions' => $predictions
                // You can include additional data if needed
            ];
    
            // Return the predictions as JSON response
            return response()->json($result, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            error_log($e->getMessage());
            return response()->json(['error' => 'An error occurred while predicting values.'], 500);
        }
    }
    
    public function getDataGraphByYearX2(Request $request){
        try {
            // error_log($id . "test");
            $req = $request->all();

            // error_log(json_encode($req));

            // error_log(date('Y', strtotime($req['month'])));

            $area_id = $req['area_id'] ? $req['area_id'] : 'All';
            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');

            error_log($area_id . " " . $month . " " . $year);

            $params = [
                'month' => $month,
                'area_id' => $area_id,
                'year' => $year
            ];
            

            $dt = $this->linearRegressionRepository->getDataGraphByYearX2(json_decode(json_encode($params)));

            // $forecasted = $dt['y'][array_keys($dt['y'])[0]];
            // error_log(json_encode($dt));
            // get the value of $dt['y'] using the last key

            return response()->json( $dt, 200);

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
    public function getStemDataGraphByYearP(Request $request){
        try {
            // error_log(json_encode($req));

            $req = $request->all();

            $area_id = $req['area_id'] ? $req['area_id'] : 'All';
            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');

            $params = [
                'month' => $month,
                'area_id' => $area_id,
                'year' => $year
            ];

            error_log(json_encode($params) . " test");
            

            $dt = $this->linearRegressionRepository->getStemDataGraphByYearP(json_decode(json_encode($params)));

            // $forecasted = $dt['y'][array_keys($dt['y'])[0]];
            // error_log(json_encode($dt) . "test");
            // get the value of $dt['y'] using the last key

            return response()->json( $dt, 200);

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function getStemDataGraphByYear(Request $request){
        try {
            // error_log(json_encode($req));

            $req = $request->all();

            $area_id = $req['area_id'] ? $req['area_id'] : 'All';
            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');

            $params = [
                'month' => $month,
                'area_id' => $area_id,
                'year' => $year
            ];

            error_log(json_encode($params) . " test");
            

            $dt = $this->linearRegressionRepository->getStemDataGraphByYear(json_decode(json_encode($params)));

            // $forecasted = $dt['y'][array_keys($dt['y'])[0]];
            // error_log(json_encode($dt) . "test");
            // get the value of $dt['y'] using the last key

            return response()->json( $dt, 200);

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function getDataGraphByYearAll(Request $request){
        try {
            // error_log($id . "test");
            $req = $request->all();

            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');

            

            $params = [
                'month' => $month,
                'year' => $year
            ];
            

            $dt = $this->linearRegressionRepository->getDataGraphByYearAll(json_decode(json_encode($params)));

            // $forecasted = $dt['y'][array_keys($dt['y'])[0]];
            // error_log(json_encode($dt));
            // get the value of $dt['y'] using the last key

            return response()->json( $dt, 200);

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
   
    public function getStemDataGraphByYearAll(Request $request){
        try {
            // error_log(json_encode($req));

            $req = $request->all();

            $month = $req['month'] ? date('m', strtotime($req['month'])) : date('m');
            $year = $req['month'] ? date('Y', strtotime($req['month'])) : date('Y');

            $params = [
                'month' => $month,
                'year' => $year
            ];

            error_log(json_encode($params) . " test");
            

            $dt = $this->linearRegressionRepository->getStemDataGraphByYearAll(json_decode(json_encode($params)));

            // $forecasted = $dt['y'][array_keys($dt['y'])[0]];
            // error_log(json_encode($dt) . "test");
            // get the value of $dt['y'] using the last key

            return response()->json( $dt, 200);

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
