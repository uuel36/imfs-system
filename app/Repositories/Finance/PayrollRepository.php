<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Payroll;
use App\Models\Finance\PayrollItem;
use App\Models\Finance\Philhealth;
use App\Models\Finance\SSS;
use App\Models\HR\Employee;
use App\Models\Finance\Archive;
use App\Models\Setting\FinanceSetting;
use App\Models\Setting\OvertimeSetting;
use App\Repositories\Leadman\AttendanceRepository;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollRepository extends Repository {

    private $attendanceRepository;
    private $archive;

    public function __construct(Payroll $model, AttendanceRepository $attendanceRepository, Archive $archive) {

        $this->attendanceRepository = $attendanceRepository;
        $this->archive = $archive;
        parent::__construct($model);

    }


    public function getCurrentPayrollGenerateDate() {

        $date = $this->model()->orderBy('id', 'desc')->first();

        if($date) {

            return $date->to_date;

        } else {
            // deduct 1 day from current date
            // return Carbon::now()->format('Y-m-d');
            return Carbon::now()->subDay()->format('Y-m-d');
        }
    }

    public function getArchivePayrolls($params) {

        $payrolls = DB::table("payrolls")
            ->select("payrolls.*", "employees.firstname", "employees.lastname", "employees.middlename", "employees.position_id", "positions.name as position_name")
            ->join("employees", "employees.id", "=", "payrolls.employee_id")
            ->join("positions", "positions.id", "=", "employees.position_id")
            ->where("payrolls.employee_id", $params->id);

        if($params->search) {
                
            $payrolls = $payrolls->where(function($query) use ($params) {
                $query->where(function ($query) use ($params) {
                    $query->orWhere('firstname', 'LIKE', "%$params->search%");
                    $query->orWhere('lastname', 'LIKE', "%$params->search%");
                    $query->orWhere('middlename', 'LIKE', "%$params->search%");
                });
            });
        }

        $payrolls = $payrolls->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $payrolls;

    }

    public function getPayrolls($params) {

        $payrolls = $this->model()->with(['employee' => function($query) {
            $query->with(['position']);
        }]);
            // ->where(\DB::raw("(DATE_FORMAT(to_date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'));

            if($params->search) {

                $payrolls = $payrolls->where(function($query) use ($params) {
                    $query->whereHas('employee', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->orWhere('firstname', 'LIKE', "%$params->search%");
                            $query->orWhere('lastname', 'LIKE', "%$params->search%");
                            $query->orWhere('middlename', 'LIKE', "%$params->search%");
                        });
                    });
                })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

                return $payrolls;

            }

            $payrolls = $payrolls->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $payrolls;

    }

    public function getHistoryPayrolls($params) {

        $payrolls = $this->model()->with(['employee' => function($query) {
            $query->with(['position']);
        }])->where(DB::raw("(DATE_FORMAT(from_date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'));

        if($params->search) {

            $payrolls = $payrolls->where(function($query) use ($params) {
                $query->whereHas('employee', function ($query) use ($params) {
                    $query->where(function ($query) use ($params) {
                        $query->orWhere('firstname', 'LIKE', "%$params->search%");
                        $query->orWhere('lastname', 'LIKE', "%$params->search%");
                        $query->orWhere('middlename', 'LIKE', "%$params->search%");
                    });
                });
            })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $payrolls;

        }

        $payrolls = $payrolls->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        return $payrolls;

    }

    public function getMergedPayroll($params){
        $payrolls = $this->model()->with(['employee' => function($query) {
            $query->with(['position']);
        }]);
            // ->where(\DB::raw("(DATE_FORMAT(to_date,'%d-%m-%Y'))"), (new Carbon($params->date))->format('d-m-Y'));

            if($params->search) {

                // merge payroll data with the same employee id // experimental
                $payrolls = $payrolls->orderBy('id', 'desc')->get();
                $payrolls = $payrolls->groupBy('employee_id');
                $payrolls = $payrolls->map(function($item, $key) {
                    $item = $item->map(function($item, $key) {
                        $item->regular = json_decode($item->regular);
                        $item->overtime = json_decode($item->overtime);
                        $item->deductions = json_decode($item->deductions);
                        return $item;
                    });
                    return $item;
                });

                $payrolls = $payrolls->where(function($query) use ($params) {
                    $query->whereHas('employee', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->orWhere('firstname', 'LIKE', "%$params->search%");
                            $query->orWhere('lastname', 'LIKE', "%$params->search%");
                            $query->orWhere('middlename', 'LIKE', "%$params->search%");
                        });
                    });
                })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);


                return $payrolls;

            }

            // merge payroll data with the same employee id // experimental
            $payrolls = $payrolls->orderBy('id', 'desc')->get();
            $payrolls = $payrolls->groupBy('employee_id');
            $payrolls = $payrolls->map(function($item, $key) {
                $item = $item->map(function($item, $key) {
                    $item->regular = json_decode($item->regular);
                    $item->overtime = json_decode($item->overtime);
                    $item->deductions = json_decode($item->deductions);
                    return $item;
                });
                return $item;
            });

            error_log(json_encode($payrolls));

            $payrolls = $payrolls->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);



            return $payrolls;
    }

    public function storePayroll($request) {
        foreach ($request->payroll as $key => $req) {
            // Check if the payroll data already exists for the specified employee and dates
            $existingPayroll = $this->model()
                ->where('employee_id', $req->employee_id)
                ->where('from_date', $request->date_from)
                ->where('to_date', $request->date_to)
                ->first();
    
            if ($existingPayroll) {
                // Update existing payroll data
                $existingPayroll->employee_id = $req->employee_id;
                $existingPayroll->from_date = $request->date_from;
                $existingPayroll->to_date = $request->date_to;
                $existingPayroll->rate = $request->rate;
                $existingPayroll->date = Carbon::now();
                $existingPayroll->regular = $req->records;
                $existingPayroll->overtime = $req->overtime;
                $existingPayroll->deductions = $req->records[0]->deductions;
                $existingPayroll->save(); // Update the existing payroll data
                $this->createArchiveRecord($existingPayroll->from_date, $existingPayroll->date);
            } else {
                // Create new payroll data if it doesn't exist
                $data = new $this->model();
                $data->employee_id = $req->employee_id;
                $data->from_date = $request->date_from;
                $data->to_date = $request->date_to;
                $data->rate = $request->rate;
                $data->date = Carbon::now();
                $data->regular = $req->records;
                $data->overtime = $req->overtime;
                $data->deductions = $req->records[0]->deductions;
                $data->save(); // Save the data in the 'payrolls' table
                $this->createArchiveRecord($data->from_date, $data->date);
            }
        }
    }
    public function getArchiveRecords($params) {
        $archiveRecords = Archive::query();
    
        if ($params->search) {
            $archiveRecords->where(function ($query) use ($params) {
                $query->where('from_date', 'LIKE', "%$params->search%")
                    ->orWhere('date', 'LIKE', "%$params->search%");
            });
        }
    
        // You can add more conditions or sorting logic if needed
        // For example, let's sort by 'id' in descending order:
        $archiveRecords->orderBy('id', 'desc');
    
        return $archiveRecords->paginate($params->count, ['*'], 'page', $params->page);
    }
    public function createArchiveRecord($fromDate, $date) {
        // Check if a record with the given 'from_date' already exists in the Archive model
        $existingRecord = Archive::where('from_date', $fromDate)->first();
    
        if ($existingRecord) {
            // Update the existing record
            $existingRecord->update(['from_date' => $fromDate, 'date' => $date]);
        } else {
            // Create a new record if it doesn't exist
            $archive = new Archive();
            $archive->from_date = $fromDate;
            $archive->date = $date;
            $archive->save();
        }
    }
    
    public function generatePayroll($request) {

        $check = $this->model()->where('employee_id', $request->employee_id)
            ->where('from_date', $request->date_from)
            ->where('to_date', $request->date_to)->first();

        if(!empty($check)) {
            return 'already';
        }
        else {
            // $check2 = $this->model()->where('employee_id', $request->employee_id)
            // ->where('from_date', '' , $request->date_from)
            // ->where('to_date', $request->date_to)->first();
        }

        $attendance = $this->attendanceRepository->geAttendanceByIDdate($request);

        return $attendance;

    }

    public function getAllPayrolls($request){

        // get all attendance
        $attendance = $this->attendanceRepository->getAllAttendance($request);
        $deductions = [];

        $data = ['attendance' => $attendance, 'deductions' => $deductions];

        return $attendance;

    }

    public function generatePayslip($id) {


        $payroll = $this->model()->with(['employee', 'item'])->find($id);

        return $payroll;

    }

    public function getOvertimeRate() {

        $overtime = FinanceSetting::first();

        return $overtime;

    }

    public function updateFinanceSetting($id, $request) {

        $setting = FinanceSetting::find($id);
        $setting->overtime_rate_hour = $request->overtime_rate_hour;
        if($setting->save()) {

            return $setting;

        }

    }

    public function getEmployeeDeduuction($employee_id) {

        $employee = Employee::find($employee_id);

        $deductions = [];

        if($employee) {

            $financeSetting = FinanceSetting::first();

            if($employee->philhealth && $employee->is_contribution == 1) {

                $philhealth = Philhealth::all();
                $deduction = 0;
                $employee->salary = $employee->salary * 25;

                if($philhealth) {
                    foreach ($philhealth as $value) {

                        if($employee->salary >= $value->from && $employee->salary <= $value->to) {
                            $deduction = $value->deduction;
                        }

                    }

                    array_push($deductions, ['type' => 'PhilHealth', 'amount' => $deduction]);

                }

            }

            if($employee->sss && $employee->is_contribution == 1) {

                $sss = SSS::all();
                $deduction = 0;
                if($sss) {
                    foreach ($sss as $value) {

                        if($employee->salary >= $value->from && $employee->salary <= $value->to) {
                            $deduction = $value->deduction;
                        }

                    }

                    array_push($deductions, ['type' => 'SSS', 'amount' => $deduction]);

                }
            }
        }

        return $deductions;

    }
}
