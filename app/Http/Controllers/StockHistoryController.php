<?php

namespace App\Http\Controllers;

use App\Repositories\Warehouse\StockHistoryRepository;
use Illuminate\Http\Request;

class StockHistoryController extends Controller
{
    private $stockHistoryRepository;
    public function __construct(StockHistoryRepository $stockHistoryRepository) {
        $this->stockHistoryRepository = $stockHistoryRepository;
    }

    public function getHistories(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search
        ];

        $histories = $this->stockHistoryRepository->getHistory(json_decode(json_encode($params)));

        return response()->json($histories, 200);

    }
}
