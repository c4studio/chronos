<?php

namespace Chronos\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaController extends Controller
{

    public function index()
    {
         return view('chronos::content.media.index');
    }

}
