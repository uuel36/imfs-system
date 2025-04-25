<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Repositories\Finance\PositionRepository;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    private $positionRepository;

    public function __construct(PositionRepository $positionRepository) {

        $this->positionRepository = $positionRepository;

    }

    public function getRoleList() {

        $roles = $this->positionRepository->getRoleList();

        return response()->json($roles, 200);

    }

    public function getPositions(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search
        ];

        $positions = $this->positionRepository->getPositions(json_decode(json_encode($params)));

        return response()->json($positions, 200);
    }

    public function storePosition(Request $request) {

        $position = $this->positionRepository->storePosition(json_decode(json_encode($request->all())));

        return response()->json($position, 200);

    }

    public function updatePosition($id, Request $request) {

        $position = $this->positionRepository->updatePosition($id, json_decode(json_encode($request->all())));

        return response()->json($position, 200);

    }

    public function deletePosition($id) {

        $position = $this->positionRepository->deletePosition($id);

        return response()->json($position, 200);

    }

    public function getPositionList() {

        $positions = $this->positionRepository->getPositionList();

        return response()->json($positions, 200);

    }
}
