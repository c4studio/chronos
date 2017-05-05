<?php

namespace Chronos\Scaffolding\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Chronos\Scaffolding\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;

class AccessTokensController extends Controller
{

    public function index()
    {
        return view('chronos::settings.access_tokens.index');
    }

}
