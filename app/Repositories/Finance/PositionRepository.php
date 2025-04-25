<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Position;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class PositionRepository extends Repository {

    public function __construct(Position $model) {

        parent::__construct($model);

    }

    public function getRoleList() {

        $roles = DB::table('roles')->get();

        return $roles;

    }

    public function getPositions($params) {

        $positions = $this->model();

        if($params->search) {

            $positions = $positions->where(function($query) use ($params) {
                $query->where('name', 'LIKE', "%$params->search%");
            })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $positions;

        }
        $positions = $positions->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $positions;

    }

    public function storePosition($request) {

        $data = new $this->model();

        $is_deploy = $request->is_deploy ? 1 : 0;
        $data->name = $request->name;
        $data->rate = $request->rate;
        $data->is_deploy = $is_deploy;

        if($data->save()) {

            return $data;

        }

    }

    public function updatePosition($id, $request) {

        $data = $this->model()->find($id);
        $is_deploy = $request->is_deploy ? 1 : 0;
        $data->name = $request->name;
        $data->rate = $request->rate;
        $data->is_deploy = $is_deploy;

        if($data->save()) {

            return $data;

        }

    }

    public function deletePosition($id) {

        $data = $this->model()->find($id);

        if($data) {
            $data->delete();
        }

    }

    public function getPositionList() {

        $data = $this->model()->get();

        return $data;

    }

}
