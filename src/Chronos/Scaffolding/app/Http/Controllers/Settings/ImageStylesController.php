<?php

namespace Chronos\Scaffolding\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Chronos\Scaffolding\Models\AccessToken;
use Chronos\Scaffolding\Models\ImageStyle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Passport\Token;

class ImageStylesController extends Controller
{

    public function index()
    {
        return view('chronos::settings.image_styles.index');
    }

    public function create()
    {
        return view('chronos::settings.image_styles.create');
    }

    public function edit(ImageStyle $style)
    {
        return view('chronos::settings.image_styles.edit')->with('style', $style);
    }

}
