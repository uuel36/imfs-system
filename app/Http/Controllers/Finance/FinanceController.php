<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Setting\FinanceSetting;
use App\Repositories\Finance\PayrollRepository;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;

class FinanceController extends Controller
{

    private $payrollRepository;

    public function __construct(PayrollRepository $payrollRepository) {

        $this->payrollRepository = $payrollRepository;

    }

 

    public function index() {

        return view('finance.payroll.index');

    }

    public function getCurrentPayrollGenerateDate() {

        $date = $this->payrollRepository->getCurrentPayrollGenerateDate();

        return response()->json($date, 200);

    }
   
    public function financeSetting() {
        $setting = FinanceSetting::first();
        return view('finance_setting.index', compact('setting'));
    }

    public function getPayrolls(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;
        $date = $request->date ? $request->date : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
            
        ];

        $payrolls = $this->payrollRepository->getPayrolls(json_decode(json_encode($params)));

        return response()->json($payrolls, 200);

    }

   
    public function getArchivePayrolls(Request $request) {

        try {
            $page = $request->page ? $request->page : 1;
            $count = $request->count ? $request->count : 10;
            $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;
            $date = $request->date ? $request->date : null;
            $id = $request->id ? $request->id : null;

            $params = [
                'page' => $page,
                'count' => $count,
                'search' => $search,
                'date' => $date,
                'id' => $id,
            ];

            $payrolls = $this->payrollRepository->getArchivePayrolls(json_decode(json_encode($params)));    
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }

        return response()->json($payrolls, 200);

    }

    public function getHistoryPayrolls(Request $request) {

        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;
        $date = $request->date ? $request->date : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
            'date' => $date,
        ];

        error_log(json_encode($params));

        $payrolls = $this->payrollRepository->getHistoryPayrolls(json_decode(json_encode($params)));

        return response()->json($payrolls, 200);

    }

    public function storePayroll(Request $request) {

        $payrrol = $this->payrollRepository->storePayroll(json_decode(json_encode($request->all())));

        return response()->json($payrrol, 200);

    }
    public function getArchiveRecords(Request $request) {
        $page = $request->page ? $request->page : 1;
        $count = $request->count ? $request->count : 10;
        $search = $request->search && $request->search != '' && $request->search !== 'null' ? $request->search : null;
        $date = $request->date ? $request->date : null;

        $params = [
            'page' => $page,
            'count' => $count,
            'search' => $search,
            
        ];

        $archiveRecords = $this->payrollRepository->getArchiveRecords(json_decode(json_encode($params)));

        return response()->json($archiveRecords, 200);
    }


    public function generatePayroll(Request $request) {

        error_log(json_encode($request->all()));

        $payroll = $this->payrollRepository->generatePayroll(json_decode(json_encode($request->all())));

        return response()->json($payroll, 200);

    }

    public function getAllPayrolls(Request $request) {

        $payrolls = $this->payrollRepository->getAllPayrolls(json_decode(json_encode($request->all())));

        error_log(json_encode($payrolls));

        return response()->json($payrolls, 200);

    }

    public function generatePayslip($id) {

        $payroll = $this->payrollRepository->generatePayslip($id);
        $salary = 0;
        $hours = 0 ;
        foreach ($payroll->item as $item) {
            $salary += $item->salary;
            $hours += $item->hours - 1;

        }

        $data = [
            'name' => $payroll->employee->lastname.", ".$payroll->employee->firstname." ".$payroll->employee->middlename,
            'date' => Carbon::now()->format('Y-m-d'),
            'salary' => $salary,
            'hours' => $hours,
            'from' => $payroll->from_date,
            'to' => $payroll->to_date,
            'rate' => $payroll->rate,
            'items' => $payroll->item,

        ];

        $pdf = DomPDFPDF::loadView('payslip', $data);




        return $pdf->download($payroll->employee->lastname.'-payslip.pdf');
    }

    public function getOvertimeRate() {

        $overtime = $this->payrollRepository->getOvertimeRate();

        return response()->json($overtime, 200);

    }

    public function updateFinanceSetting($id, Request $request) {

        $setting = $this->payrollRepository->updateFinanceSetting($id, json_decode(json_encode($request->all())));

        return response()->json($setting, 200);

    }

    public function getEmployeeDeduuction($employee_id) {

        $deduction = $this->payrollRepository->getEmployeeDeduuction($employee_id);

        return response()->json($deduction, 200);

    }
}
