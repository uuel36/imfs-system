<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse\StockHistory;
use App\Repositories\Repository;

class StockHistoryRepository extends Repository {

    public function __construct(StockHistory $model) {

        parent::__construct($model);

    }

    public function getHistory($params) {

        $histories = $this->model()->with();

        if($params->search) {

            $histories = $histories->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $histories;
        }

        $histories = $histories->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $histories;

    }

    public function storeHistory($request) {

        $history = new $this->model();
        $history->deploy_id = $request->deploy_id;
        $history->stock_id = $request->stock_id;
        $history->item_id = $request->item_id;
        $history->quantity = $request->quantity;
        $history->area = $request->area;
        $history->date = $request->date;
        $history->leadman_id = $request->leadman_id;


        if($history->save()) {

            return $history;

        }

    }

    public function updateHistory($request) {

        $history = $this->model()->where('deploy_id', $request->deploy_id)->first();
        $history->stock_id = $request->stock_id;
        $history->item_id = $request->item_id;
        $history->quantity = $request->quantity;
        $history->area = $request->area;
        $history->date = $request->date;
        $history->leadman_id = $request->leadman_id;

        if($history->save()) {

            return $history;

        }
    }

}
