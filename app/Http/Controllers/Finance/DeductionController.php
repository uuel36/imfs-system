<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Repositories\Finance\PhilhealthRepository;
use App\Repositories\Finance\SssRepository;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    private $sssRepository;
    private $philhealthRepository;

    public function __construct(SssRepository $sssRepository, PhilhealthRepository $philhealthRepository) {

        $this->sssRepository = $sssRepository;
        $this->philhealthRepository = $philhealthRepository;

    }

    public function getPhilhealth(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search
        ];

        $philhealth = $this->philhealthRepository->getPhilhealth(json_decode(json_encode($params)));

        return response()->json($philhealth, 200);
    }

    public function storePhilhealth(Request $request) {

        $philhealth = $this->philhealthRepository->storePhilhealth(json_decode(json_encode($request->all())));

        return response()->json($philhealth, 200);

    }

    public function updatePhilhealth($id, Request $request) {

        $philhealth = $this->philhealthRepository->updatePhilhealth($id, json_decode(json_encode($request->all())));

        return response()->json($philhealth, 200);

    }

    public function deletePhilhealth($id) {

        $philhealth = $this->philhealthRepository->deletePhilhealth($id);

        return response()->json($philhealth, 200);

    }

    public function getSSS(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search
        ];

        $sss = $this->sssRepository->getSSS(json_decode(json_encode($params)));

        return response()->json($sss, 200);

    }

    public function storeSSS(Request $request) {

        $sss = $this->sssRepository->storeSSS(json_decode(json_encode($request->all())));

        return response()->json($sss, 200);

    }

    public function updateSSS($id, Request $request) {

        $sss = $this->sssRepository->updateSSS($id, json_decode(json_encode($request->all())));

        return response()->json($sss, 200);

    }

    public function deleteSSS($id) {

        $sss = $this->sssRepository->deleteSSS($id);

        return response()->json($sss, 200);

    }
}
