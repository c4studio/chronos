<?php

namespace Chronos\Content\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

class LanguagesController extends Controller
{

    public function index()
    {
        return view('chronos::settings.languages.index');
    }

}
