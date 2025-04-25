<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestOrderController extends Controller
{
    public function index()
    {
        return view('request_order.index');
    }
}
