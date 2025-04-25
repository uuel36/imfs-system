<?php

namespace App\Http\Controllers\Leadman;

use App\Http\Controllers\Controller;
use App\Repositories\Leadman\DailyReportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DailyReportController extends Controller
{
    private $dailyReportRepository;

    public function __construct(DailyReportRepository $dailyReportRepository) {

        $this->dailyReportRepository = $dailyReportRepository;

    }

    public function index() {

        return view('leadman.report.index');

    }

    public function team() {
        return view('leadman.report.indexteam');
    }

    public function getReports(Request $request) {

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

        $reports = $this->dailyReportRepository->getReports(json_decode(json_encode($params)));

        return response()->json($reports, 200);

    }

    public function getTeamReport(Request $request) {
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

        error_log('getTeamReport: ' . json_encode($params));

        $reports = $this->dailyReportRepository->getTeamReport(json_decode(json_encode($params)));

        return response()->json($reports, 200);
    }

    public function storeReport(Request $request) {

        // log the request
        // error_log('storeReport: ' . json_encode($request->all()));
        // error_log('storeReport: ' . json_encode($request->all()));

        $data = [
            'date' => $request->date,
            'team_id' => $request->team_id,
            'employee_id' => $request->employee_id,
            'task_id' => $request->task_id,
            'area_id' => $request->area_id,
            'data' => $request->data,
            'leadman_id' => $request->leadman_id,
        ];

        // error_log('storeReport: ' . json_encode($));

        $report = $this->dailyReportRepository->storeReport(json_decode(json_encode($data)));

        return response()->json($report, 200);

    }

    public function updateReport($id, Request $request) {

        error_log('updateReport: ' . $id . "  " . json_encode($request->all()));

        $report = $this->dailyReportRepository->updateReport($id, json_decode(json_encode($request->all())));

        return response()->json($report, 200);

    }

    public function deleteReport($id) {

        $report = $this->dailyReportRepository->deleteReport($id);

        return response()->json($report, 200);

    }
}
