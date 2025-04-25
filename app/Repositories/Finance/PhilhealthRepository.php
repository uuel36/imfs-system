<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Philhealth;
use App\Repositories\Repository;

class PhilhealthRepository extends Repository {

    public function __construct(Philhealth $model) {

        parent::__construct($model);

    }

    public function getPhilhealth($params) {

        $philhealth = $this->model();

        if($params->search) {

            $philhealth = $philhealth->where(function($query) use ($params) {
                $query->where('from', 'LIKE', "%$params->search%");
                $query->where('to', 'LIKE', "%$params->search%");
            })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $philhealth;

        }
        $philhealth = $philhealth->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $philhealth;

    }

    public function storePhilhealth($request) {

        $data = new $this->model();
        $data->from = $request->from;
        $data->to = $request->to;
        $data->deduction = $request->deduction;

        if($data->save()) {

            return $data;

        }

    }

    public function updatePhilhealth($id, $request) {

        $data = $this->model()->find($id);
        $data->from = $request->from;
        $data->to = $request->to;
        $data->deduction = $request->deduction;

        if($data->save()) {

            return $data;

        }

    }

    public function deletePhilhealth($id) {

        $data = $this->model()->find($id);

        if($data) {
            $data->delete();
        }

    }

}
