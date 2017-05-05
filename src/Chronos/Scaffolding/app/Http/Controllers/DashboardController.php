<?php

namespace Chronos\Scaffolding\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function index()
    {
        return view('chronos::dashboard.index');
    }

}
