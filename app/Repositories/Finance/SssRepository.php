<?php

namespace App\Repositories\Finance;

use App\Models\Finance\SSS;
use App\Repositories\Repository;

class SssRepository extends Repository {

    public function __construct(SSS $model) {

        parent::__construct($model);

    }

    public function getSSS($params) {

        $sss = $this->model();

        if($params->search) {

            $sss = $sss->where(function($query) use ($params) {
                $query->where('from', 'LIKE', "%$params->search%");
                $query->where('to', 'LIKE', "%$params->search%");
            })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $sss;

        }
        $sss = $sss->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $sss;

    }

    public function storeSSS($request) {

        $data = new $this->model();
        $data->from = $request->from;
        $data->to = $request->to;
        $data->deduction = $request->deduction;

        if($data->save()) {

            return $data;

        }

    }

    public function updateSSS($id, $request) {

        $data = $this->model()->find($id);
        $data->from = $request->from;
        $data->to = $request->to;
        $data->deduction = $request->deduction;

        if($data->save()) {

            return $data;

        }

    }

    public function deleteSSS($id) {

        $data = $this->model()->find($id);

        if($data) {
            $data->delete();
        }

    }

}
