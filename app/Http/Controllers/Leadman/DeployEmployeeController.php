<?php

namespace App\Http\Controllers\Leadman;

use App\Http\Controllers\Controller;
use App\Repositories\Leadman\DeployEmployeeRepository;
use Illuminate\Http\Request;

class DeployEmployeeController extends Controller
{
    private $deployEmployeeRepository;

    public function __construct(DeployEmployeeRepository $deployEmployeeRepository) {

        $this->deployEmployeeRepository = $deployEmployeeRepository;

    }

    public function index() {

        return view('leadman.deploy.index');

    }

    public function getLeadmans() {

        $leadmans = $this->deployEmployeeRepository->getLeadmans();

        return response()->json($leadmans, 200);

    }

    public function getDeployByTeamIdAndDate(Request $request){

        $teamId = $request->team_id ? $request->team_id : null;
        $date = $request->date ? $request->date : null;

        $params = [
            'teamId' => $teamId,
            'date' => $date
        ];
    
        $deploy = $this->deployEmployeeRepository->getDeployByTeamIdAndDate(json_decode(json_encode($params)));
    
        return response()->json($deploy, 200);
    
    }

    public function searchDeployByDate(Request $request) {

        $date = $request->date ? $request->date : null;
        $team_id = $request->team_id ? $request->team_id : null;
        $params = [
            'date' => $date,
            'team_id' => $team_id
        ];
        

        $deploy = $this->deployEmployeeRepository->searchDeployByDate(json_decode(json_encode($params)));
        error_log($deploy);

        return response()->json($deploy, 200);

    }

    public function getDeploy(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;
        $date = $request->date ? $request->date : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
            'date' => $date,
        ];

        $deploy = $this->deployEmployeeRepository->getDeploy(json_decode(json_encode($params)));

        return response()->json($deploy, 200);
    }

    public function storeDeploy(Request $request) {

        // dd($request->all());

        $deploy = $this->deployEmployeeRepository->storeDeploy(json_decode(json_encode($request->all())));

        return response()->json($deploy, 200);

    }

    public function updateDeploy($id, Request $request) {

        $deploy = $this->deployEmployeeRepository->updateDeploy($id, json_decode(json_encode($request->all())));

        return response()->json($deploy, 200);

    }

    public function deleteDeploy($id) {

        $deploy = $this->deployEmployeeRepository->deleteDeploy($id);

        return response()->json($deploy, 200);
    }

    public function getDeployByArea(Request $request) {

        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $date = $request->date ? $request->date : null;

        $params = [
            'search' => $search,
            'date' => $date,
        ];

        $team = $this->deployEmployeeRepository->getDeployByArea(json_decode(json_encode($params)));

        return response()->json($team, 200);
    }
}
