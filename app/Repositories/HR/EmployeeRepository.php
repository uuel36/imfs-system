<?php

namespace App\Repositories\HR;

use App\Models\HR\Employee;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeRepository extends Repository {

    private $qrCodeRepository;
    public function __construct(Employee $model, QrCodeRepository $qrCodeRepository) {
        $this->qrCodeRepository = $qrCodeRepository;
        parent::__construct($model);

    }

    public function getEmployeeTeam($id) {

        $employee = DB::table('team_members')->where('employee_id', $id)->get();

        // get team name
        $team = Db::table('teams')->where('id', $employee[0]->team_id)->get();

        //error_log($team);

        return json_encode($team[0]);

    }

    public function getEmployees($params) {

        $employees = $this->model()->with(['position']);

        if($params->search) {

            $employees = $employees->where(function ($query) use ($params) {
                $query->orWhere('firstname', 'like', "%$params->search%");
                $query->orWhere('middlename', 'like', "%$params->search%");
                $query->orWhere('lastname', 'like', "%$params->search%");
                $query->orWhere('position', 'like', "%$params->search%");
                $query->orWhere('gender', 'like', "%$params->search%");
                $query->orWhere('birthday', 'like', "%$params->search%");
                $query->orWhere('suffix', 'like', "%$params->search%");
            })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $employees;

        }

        $employees = $employees->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $employees;

    }

    public function storeEmployee($request) {

        $data = new $this->model();

        $is_contribution = $request->is_contribution ? 1 : 0;

        $data->firstname = $request->firstname;
        $data->lastname = $request->lastname;
        $data->middlename = $request->middlename;
        $data->suffix = $request->suffix;
        $data->birthday = $request->birthday;
        $data->gender = $request->gender;
        $data->contact = $request->contact;
        $data->position_id = $request->position_id;
        $data->sss = $request->sss;
        $data->is_contribution = $is_contribution;
        $data->salary = $request->salary;
        $data->philhealth = $request->philhealth;
        $data->address = $request->address;
        $data->qrcode = $request->qrcode;



        if($request->hasFile('file')) {
            $folder = '/employee/profile/';
            $media = $request->file('file');
            $name = time() . '.' . $media->extension();
            $link = $media->storeAs($folder, $name, 'public');

            $data->profile = $name;
            $data->profile_path = $folder;
            $data->profile_name = $request->file_name;
        }

        $this->qrCodeRepository->updateQrCodeData($data->qrcode);

        if($data->save()) {

            return $this->model()->with(['position'])->find($data->id);
        }

    }

    public function updateEmployee($id, $request) {

        $position_id = $request->position_id_id ? $request->position_id_id : $request->position_id;

        $is_contribution = $request->is_contribution ? 1 : 0;

        $data = $this->model()->find($id);

        $data->firstname = $request->firstname;
        $data->lastname = $request->lastname;
        $data->middlename = $request->middlename;
        $data->suffix = $request->suffix;
        $data->birthday = $request->birthday;
        $data->gender = $request->gender;
        $data->contact = $request->contact;
        $data->position_id = $position_id;
        $data->sss = $request->sss;
        $data->is_contribution = $is_contribution;
        $data->salary = $request->salary;
        $data->philhealth = $request->philhealth;
        $data->address = $request->address;

        if($request->hasFile('file')) {

            Storage::disk('public')->delete("/employee/profile/". $data->profile);

            $folder = '/employee/profile/';
            $media = $request->file('file');
            $name = time() . '.' . $media->extension();
            $link = $media->storeAs($folder, $name, 'public');


            $data->profile = $name;
            $data->profile_path = $folder;
            $data->profile_name = $request->file_name;
        }
        else if($request->remove_file) {

            Storage::disk('public')->delete("/employee/profile/". $data->profile);
            $data->profile = '';
            $data->profile_path = '';
            $data->profile_name = '';
        }

        if($data->save()) {

            return $this->model()->with(['position'])->find($id);
        }
    }

    public function deleteEmployee($id) {
        $data = $this->model()->find($id);
        if($data) {
            $data->delete();
        }
    }

    public function searchEmployee($params) {

        $employee = $this->model()->with(['position']);

        if($params->search) {

            $employee = $employee->where(function ($query) use ($params) {
                $query->orWhere('firstname', 'LIKE', "%$params->search%");
                $query->orWhere('lastname', 'LIKE', "%$params->search%");
            })->limit(20)->get();

            return $employee;

        }

    }

    public function searchEmployeeMember($params) {

        $employee = $this->model()->with(['position']);

        if($params->search) {

            $employee = $employee->where(function ($query) use ($params) {
                $query->orWhere('firstname', 'LIKE', "%$params->search%");
                $query->orWhere('lastname', 'LIKE', "%$params->search%");
            })->with('teamMember')->limit(20)->get();

            foreach ($employee as $key => $value) {
                $value->team_status = $value->team_member;
            }

            return $employee;

        }
    }

    public function findEmployeeByQrCode($qrcode) {

        $employee = $this->model()->with(['position'])->where('qrcode', $qrcode)->first();
        if(empty($employee)) {
            return 'no_employee';
        }
        return $employee;

    }

    public function getProfile($id) {
        $employee = $this->model()->with(['position'])->find($id);

        return $employee;
    }
}
