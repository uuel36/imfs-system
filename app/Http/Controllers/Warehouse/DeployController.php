<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\DeployRepository;
use Illuminate\Http\Request;

class DeployController extends Controller
{
    private $deployRepository;

    public function __construct(DeployRepository $deployRepository) {

        $this->deployRepository = $deployRepository;

    }

    public function returnTool($id) {

        error_log($id);

        $deploy = $this->deployRepository->returnTool($id);

        error_log($deploy);

        return response()->json($deploy, 200);
    }

    public function getReturnTools(){
            
            $tools = $this->deployRepository->getReturnTools();
    
            return response()->json($tools, 200);
    }

    public function getTools(Request $request){

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $date = $request->date ? $request->date : null;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
            'date' => $date,
        ];
            
        $tools = $this->deployRepository->getTools(json_decode(json_encode($params)));

        return response()->json($tools, 200);
    }

    public function index() {

        return view('warehouse.deploy.index');

    }

    public function getDeploy(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $date = $request->date ? $request->date : null;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
            'date' => $date,
        ];

        $deploy = $this->deployRepository->getDeploy(json_decode(json_encode($params)));

        return response()->json($deploy, 200);

    }
    public function getDeployNotReturned(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
        ];

        $deploy = $this->deployRepository->getDeployNotReturned(json_decode(json_encode($params)));

        return response()->json($deploy, 200);

    }
    public function getDeployNotReturnedConsumed(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
        ];

        $deploy = $this->deployRepository->getDeployNotReturnedConsumed(json_decode(json_encode($params)));

        return response()->json($deploy, 200);

    }

    public function storeDeploy(Request $request) {

        $deploy = $this->deployRepository->storeDeploy(json_decode(json_encode($request->all())));

        return response()->json($deploy, 200);

    }

    public function updateDeploy($id, Request $request) {

        $deploy = $this->deployRepository->updateDeploy($id, json_decode(json_encode($request->all())));

        return response()->json($deploy, 200);
    }

    public function deleteDeploy($id) {

        $deploy = $this->deployRepository->deleteDeploy($id);

        return response()->json($deploy, 200);
    }
}
