<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    public function index()
    {
        return view('monthly_report.index');
    }
}
