<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReturnToolController extends Controller
{
    public function index()
    {
        return view('return_tool.index');
    }
}
