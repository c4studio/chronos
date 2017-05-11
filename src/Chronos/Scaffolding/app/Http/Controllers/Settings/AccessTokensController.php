<?php

namespace Chronos\Scaffolding\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

class AccessTokensController extends Controller
{

    public function index()
    {
        return view('chronos::settings.access_tokens.index');
    }

}
